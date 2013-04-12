#!/usr/bin/perl -w

use strict;
use diagnostics;
use DBI;
use File::Basename qw();
use Filesys::DiskUsage qw();

# This script produces a list of audios that I have not heard

# TODO:
# - the sql stated below is not accurate. It assumes that every work in the database
# has been heard. Fix it to only retrieve works that have been viewed by "Mark Veltzer".

# print debug messages ?
my($debug)=0;
my($user)='';
my($password)='';
my($output)='list_not_heard.txt';

	
my($dbh)=DBI->connect('dbi:mysql:myworld',$user,$password,{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

# selecting only works which are audio (selecting all as above will NOT work...)
my($sql)='select TbWkWork.name from TbWkWork,TbWkWorkType where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\')';
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
}
$dbh->disconnect();

open(OUTPUT,'> '.$output) || die('unable to open ['.$output.'] for writing');
# now lets reviews all works 
my(@file_list)=<by_name/*/*>;
for(my($i)=0;$i<@file_list;$i++) {
	my($path)=$file_list[$i];
	my($filename, $directories, $suffix) = File::Basename::fileparse($path);
	if($debug) {
		print $filename."\n";
		print $directories."\n";
		print $suffix."\n";
	}
	if($suffix ne '') {
		warn('suffix problem with '.$path);
		next;
	}
	if(!exists($hash{$filename})) {
		#print OUTPUT $path."\n";
		my($total)=Filesys::DiskUsage::du({ 'human-readable' => 1 },$path);
		print OUTPUT $path.' - '.$total."\n";
	}
}
close(OUTPUT) || die('unable to close ['.$output.']');
