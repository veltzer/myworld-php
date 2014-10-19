#!/usr/bin/perl -w

=head

this script gets string dates from the database and reinserts them as real mysql datetime
objects...

=cut

use strict;
use diagnostics;
use Date::Manip;
use DBI;

# parameters

# the table to work on
my($p_table)='TbWkWork';
# the column to read from
my($p_read_column)='viewdate';
# the column for ids
my($p_id_column)='id';
# the column to write to
my($p_write_column)='viewdatesub';
# do debug?
my($debug)=1;

# code

sub unixdate_to_mysql($) {
	my($string)=@_;
	my($object)=Date::Manip::UnixDate($string,'%Y-%m-%d %T');
	return($object);
}

my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

my($sql)='SELECT '.$p_id_column.','.$p_read_column.' FROM '.$p_table;
my($sth)=$dbh->prepare($sql);
$sth->execute();
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($id)=$rowhashref->{$p_id_column};
	my($viewdate)=$rowhashref->{$p_read_column};
	if($debug) {
		print 'got date '.$viewdate."\n";
	}
	my($newdate)=unixdate_to_mysql($viewdate);
	if($debug) {
		print 'newdate is '.$newdate."\n";
	}
	# now update the database...
	$dbh->do('UPDATE '.$p_table.' SET '.$p_write_column.'=? WHERE '.$p_id_column.'=?',undef,$newdate,$id);
}

$dbh->commit();
$dbh->disconnect();
