#!/usr/bin/perl -w

# A script that tests a function to read all fields from a table name

use strict;
use diagnostics;
use DBI;

# parameters...

my($db_name)='myworld';
#my($db_name)='myworld';
my($db_user)='';
my($db_pass)='';
my($db_table_name)='TbWkWork';
my($debug)=1;

# here we go...

sub get_fields($$$) {
	my($dbh,$schema,$table)=@_;
	if($debug) {
		print "schema is $schema\n";
		print "table is $table\n";
	}
	my($sql)='select column_name from information_schema.columns where table_name="'.$table.'" and table_schema="'.$schema.'"';
	my($sth)=$dbh->prepare($sql);
	$sth->execute() or die "SQL Error: $DBI::errstr\n";
	my($rowhashref);
	my(%hash);
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($column_name)=$rowhashref->{'column_name'};
		$hash{$column_name}=defined;
	}
	return \$hash;
}

my($dbh)=DBI->connect('dbi:mysql:'.$db_name,$db_user,$db_pass,{ RaiseError => 1 }) or die "Connection Error: $DBI::errstr\n";

my($hash)=get_fields($dbh,$db_name,$db_table_name);

$dbh->disconnect();
