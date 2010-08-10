#!/usr/bin/perl -w

# this script creates a slug in the database.

use strict;
use diagnostics;
use DBI;
use Error qw(:try);
use File::Find qw();
use File::Basename qw();
use Perl6::Slurp qw();

# parameters
my($param_table)="TbBsCompanies";
my($param_field_from)="name";
my($param_field_to)="slug";

my($dbh);

sub to_slug($) {
	my($val)=$_[0];
	return join("-",map(lc,split(" ",$val)));
}

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
$dbh->{HandleError} =\&handle_error;

my($sql)="select $param_field_from from $param_table";
my($sth)=$dbh->prepare($sql);
$sth->execute();
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($field_val)=$rowhashref->{$param_field_from};
	my($new_slug)=to_slug($field_val);
	$dbh->do("update $param_table set $param_field_to=? where $param_field_from=?",
		undef,$new_slug,$field_val);
}

# now commit all the changes...
$dbh->commit();
# disconnect from the database
$dbh->disconnect();
