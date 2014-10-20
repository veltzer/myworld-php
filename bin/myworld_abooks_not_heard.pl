#!/usr/bin/perl -w

=head

This script produces a list of audios that I have not heard

TODO:
- the sql stated below is not accurate. It assumes that every work in the database
has been heard. Fix it to only retrieve works that have been viewed by "Mark Veltzer".

=cut

use strict;
use diagnostics;
use DBI;
use File::Basename qw();
use Filesys::DiskUsage qw();
use Encode qw();
use utf8;
use MyUtils;

# parameters

# print debug messages ?
my($debug)=0;
# name of output file?
my($output)='list_not_heard.txt';

# code

if ($debug) {
	binmode(STDOUT, ":utf8");
}

my($dbh)=MyUtils::db_connect();

# selecting only works which are audio (selecting all as above will NOT work...)
my($sql)='SELECT TbWkWork.name FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name IN (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'audio essay\')';
if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets create a hash of all work names
my(%hash);
my($sth)=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_name)=$rowhashref->{'name'};
	$hash{$f_name}=defined;
	if($debug) {
		print 'inserted '.$f_name."\n";
	}
}
$dbh->commit();
$dbh->disconnect();

open(OUTPUT,'> '.$output) || die('unable to open ['.$output.'] for writing');
# now lets reviews all works 
my(@file_list)=<by_name/*/*>;
for(my($i)=0;$i<@file_list;$i++) {
	my($path)=$file_list[$i];
	my($filename, $directories, $suffix) = File::Basename::fileparse($path);
	$filename=Encode::decode_utf8($filename, 1);
	if($debug) {
		print $filename."\n";
		#print $directories."\n";
		#print $suffix."\n";
	}
	if($suffix ne '') {
		warn('suffix problem with '.$path);
		next;
	}
	if(!exists($hash{$filename})) {
		#print OUTPUT $path."\n";
		my($total)=Filesys::DiskUsage::du({ 'human-readable' => 1 },$path);
		print OUTPUT $path.' - '.$total."\n";
		if($debug) {
			print 'there'."\n";
		}
	} else {
		if($debug) {
			print 'not there'."\n";
		}
	}
}
close(OUTPUT) || die('unable to close ['.$output.']');
