#!/usr/bin/perl -w

=head

this script creates a slug in the database.
give it a table, a field and a field to put the slug in and it will do the rest.

=cut

use strict;
use diagnostics;
use DBI;
use Error qw(:try);
use File::Find qw();
use File::Basename qw();
use Perl6::Slurp qw();

# parameters
my($param_table)='TbBsCompanies';
my($param_field_from)='name';
my($param_field_to)='slug';
my($param_host)=$ARGV[0];
my($param_port)=$ARGV[1];
my($param_user)=$ARGV[2];
my($param_pass)=$ARGV[3];
my($param_name)=$ARGV[4];

my($dbh);

sub to_slug($) {
	my($val)=$_[0];
	return join('-',map(lc,split(' ',$val)));
}

sub handle_error() {
	my($rc)=$dbh->err;
	my($str)=$dbh->errstr;
	my($rv)=$dbh->state;
	throw Error::Simple($str.','.$rv.','.$rv);
}

$dsn='dbi:mysql:'.$param_name;
if (defined($param_host)) {
	$dns.=';host='.$param_host;
}
if (defined($param_port)) {
	$dns.=';=port'.$param_port;
}
$dbh=DBI->connect($dsn, $param_user, $param_pass, {
	RaiseError => 1,
	AutoCommit => 0,
	mysql_enable_utf8 => 1,
});
$dbh->{HandleError} =\&handle_error;

my($sql)='SELECT $param_field_from FROM $param_table';
my($sth)=$dbh->prepare($sql);
$sth->execute();
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($field_val)=$rowhashref->{$param_field_from};
	my($new_slug)=to_slug($field_val);
	$dbh->do('UPDATE $param_table SET $param_field_to=? WHERE $param_field_from=?',
		undef,$new_slug,$field_val);
}

# now commit all the changes...
$dbh->commit();
# disconnect from the database
$dbh->disconnect();
