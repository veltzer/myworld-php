#!/usr/bin/perl -w

# this script imports images into my databaseo.
#
# Currently it handles:
# - small company images

use strict;
use diagnostics;
use DBI;
use Error qw(:try);
use File::Find qw();
use File::Basename qw();
use Perl6::Slurp qw();

# parameters
my($param_table)="TbBsCompanies";
my($param_field_update)="smallImage";
my($param_field_name)="slug";

my($debug)=0;

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
