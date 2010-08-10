#!/usr/bin/perl -w

# this script imports stuff into my databaseo.
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
$dbh->{HandleError} =\&handle_error;

my(@list)=<images/companies/target/sma/*.png>;
for(my($i)=0;$i<@list;$i++) {
	my($curr)=$list[$i];
	print "curr is $curr\n";
}

# now commit all the changes...
$dbh->commit();
# disconnect from the database
$dbh->disconnect();
