#!/usr/bin/perl -w

# this script eases the work of refactoring tables.
# YOU need to massage it a bit in order to fit it for a specific refactoring
# but not a whole lot.
# It is ideal for the following purpse:
# - You have a table A of which you want to remove fields fa1,...,faN
# - You want to move the fields to table B.
# - You want A to retain an index to the rows that were moved to B.
# - A may have repeats of data but B should not have them.

use strict;
use diagnostics;

use DBI;
my($dbh)=DBI->connect('dbi:mysql:myworld','','',{ RaiseError => 1 }) or die "Connection Error: $DBI::errstr\n";

# hash to store values in the original table
my(%orig_hash);

my($sql)="select * from TbWkWork";
my($sth)=$dbh->prepare($sql);
$sth->execute() or die "SQL Error: $DBI::errstr\n";
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($row_id)=$rowhashref->{'id'};
	my($name)=$rowhashref->{'author_firstname'}." ".$rowhashref->{'author_surname'};
	my($id);
	if(exists($orig_hash{$name})) {
		$id=$orig_hash{$name};
	} else {
		$dbh->do("INSERT INTO TbIdPerson (firstname,surname,othername) VALUES(?,?,?)",
			undef,
			$rowhashref->{'author_firstname'},
			$rowhashref->{'author_surname'},
			$rowhashref->{'author_othername'}
		);
		$id=$dbh->last_insert_id(undef,undef,undef,undef);
		$orig_hash{$name}=$id;
	}
	$dbh->do("update TbWkWork set authorId=? where id=?",undef,$id,$row_id);
} 
$dbh->disconnect();
