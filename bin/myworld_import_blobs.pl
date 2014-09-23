#!/usr/bin/perl -w

# this script a blob into my database.
#
# Currently it handles:
# - small company images
#
# TODO:
# - import should be either database driven or disk driven (today it is disk driven).
# (which means we should first query the database for missing images and then
# find them on the disk and import them. Then, as bonus, we could also print a message
# if there were images on the disk which were not imported).

use strict;
use diagnostics;
use DBI;
use Error qw(:try);
use File::Find qw();
use File::Basename qw();
use Perl6::Slurp qw();
use MIME::Types qw();
use File::MimeInfo qw();
use File::Slurp qw();

# parameters

# table to update
my($param_table)='TbImage';
# field by which to do the update...
my($param_field_name)='slug';
# should we debug the script ?
my($debug)=1;
# print stats at the end ?
my($stats)=1;
# do work ?
my($do_work)=1;
# should we die if there are organizations with no images ?
my($noimage_die)=1;

# here starts the script...
my($imported)=0;
my($dbh);


sub handle_error() {
	my($rc)=$dbh->err;
	my($str)=$dbh->errstr;
	my($rv)=$dbh->state;
	throw Error::Simple($str.','.$rv.','.$rv);
}

$dbh=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	AutoCommit => 0,
	mysql_enable_utf8 => 1,
});
#my $mimetypes = MIME::Types->new;
#$dbh->{HandleError} =\&handle_error;

my(@list_small)=<images/organizations/target/sma/*.png>;
my(@list_large)=<images/organizations/target/big/*.png>;
my(@list_orig)=<images/organizations/src/*>;
for(my($i)=0;$i<@list_small;$i++) {
	my($curr_small)=$list_small[$i];
	my($curr_large)=$list_large[$i];
	my($curr_orig)=$list_orig[$i];
	my($name,$path,$suffix)=File::Basename::fileparse($curr_small,'.png');
	if($debug) {
		print 'curr_small is ['.$curr_small.']'."\n";
		print 'curr_large is ['.$curr_large.']'."\n";
		print 'curr_orig is ['.$curr_orig.']'."\n";
		print 'name is ['.$name.']'."\n";
	}
	# read the file into RAM
	my($blob_small,$blob_large,$blob_orig);
	$blob_small=File::Slurp::read_file($curr_small);
	$blob_large=File::Slurp::read_file($curr_large);
	$blob_orig=File::Slurp::read_file($curr_orig);
	# get the mime types
	my($mime_small)=File::MimeInfo::mimetype($curr_small);
	my($mime_large)=File::MimeInfo::mimetype($curr_large);
	my($mime_orig)=File::MimeInfo::mimetype($curr_orig);
	if($debug) {
		print 'mime_small is ['.$mime_small.']'."\n";
		print 'mime_large is ['.$mime_large.']'."\n";
		print 'mime_orig is ['.$mime_orig.']'."\n";
		print 'length(small) is ['.length($blob_small).']'."\n";
		print 'length(large) is ['.length($blob_large).']'."\n";
		print 'length(orig) is ['.length($blob_orig).']'."\n";
	}

	my($str_small)='UPDATE '.$param_table.' SET smallData=?,smallMime=? WHERE '.$param_field_name.'=?';
	my($str_large)='UPDATE '.$param_table.' SET largeData=?,largeMime=? WHERE '.$param_field_name.'=?';
	my($str_orig)='UPDATE '.$param_table.' SET origData=?,origMime=? WHERE '.$param_field_name.'=?';
	if($debug) {
		print 'str_small is ['.$str_small.']'."\n";
		print 'str_large is ['.$str_large.']'."\n";
		print 'str_orig is ['.$str_orig.']'."\n";
	}
	if($do_work) {
		my($rows_small,$rows_large,$rows_orig);
		$rows_small=$dbh->do($str_small,undef,$blob_small,$mime_small,$name);
		$rows_large=$dbh->do($str_large,undef,$blob_large,$mime_large,$name);
		$rows_orig=$dbh->do($str_orig,undef,$blob_orig,$mime_orig,$name);
		if($rows_small!=1 || $rows_large!=1 || $rows_orig!=1) {
			die('wrong number of rows...');
		}
	}
	$imported++;
}
if($noimage_die && $do_work) {
	my($sql)='SELECT COUNT(*) FROM '.$param_table.' WHERE origData IS NULL';
	my($sth)=$dbh->prepare($sql);
	$sth->execute();
	my(@arr)=$sth->fetchrow_array();
	my($count)=$arr[0];
	if($count!=0) {
		die('count is ['.$count.']');
	}
	$sth->finish();
}

if($do_work) {
	# now commit all the changes...
	$dbh->commit();
}
# disconnect from the database
$dbh->disconnect();

if($stats) {
	print 'imported is ['.$imported.']'."\n";
}
