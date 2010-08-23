#!/usr/bin/perl -w

# this script imports images into my databaseo.
#
# Currently it handles:
# - small company images
#
# TODO:
# - only import images which are not in the database.
# - import should be database driven and not disk driven
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

# parameters

# table to update
my($param_table)="TbBsCompanies";
# field to update
my($param_field_update)="smallImage";
# field by which to do the update...
my($param_field_name)="slug";
# should we debug the script ?
my($debug)=0;
# print stats at the end ?
my($stats)=1;

# here starts the script...
my($imported)=0;
my($dbh);

sub handle_error() {
	my($rc)=$dbh->err;
	my($str)=$dbh->errstr;
	my($rv)=$dbh->state;
	throw Error::Simple($str.",".$rv.",".$rv);
}

$dbh=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	AutoCommit => 0,
	mysql_enable_utf8 => 1,
});
#$dbh->{HandleError} =\&handle_error;

my(@list)=<images/companies/target/sma/*.png>;
for(my($i)=0;$i<@list;$i++) {
	my($curr)=$list[$i];
	my($name,$path,$suffix)=File::Basename::fileparse($curr,".png");
	if($debug) {
		print "curr is $curr\n";
		print "name is $name\n";
	}
	# read the file into RAM
	my($dt_blob);
	$dt_blob=Perl6::Slurp::slurp($curr);

	my($str)="update $param_table set $param_field_update=? where $param_field_name=?";
	if($debug) {
		print "str is $str\n";
	}
	my($rows);
	$rows=$dbh->do($str,undef,$dt_blob,$name);
	if($rows!=1) {
		die("wrong number of rows ($rows)");
	}
	$imported++;
}
my($sql)="select count(*) from $param_table where $param_field_update is null";
my($sth)=$dbh->prepare($sql);
$sth->execute();
my(@arr)=$sth->fetchrow_array();
my($count)=$arr[0];
if($count!=0) {
	die("count is $count");
}
$sth->finish();

# now commit all the changes...
$dbh->commit();
# disconnect from the database
$dbh->disconnect();

if($stats) {
	print "imported is [$imported]\n";
}
