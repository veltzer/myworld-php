#!/usr/bin/perl -w

=head desciption 

This script deletes a single work using it's ID IN THE DATABASE.
Mind you: this is NOT according to imdbid.

TODO:
- print the name of the work to be deleted before it's deleted and confirm with the user.

=cut

use strict;
use diagnostics;
use DBI;
use MyImdb qw();
use MyUtils qw();

if(!@ARGV) {
	die 'please provide work id'."\n";
}

my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});
my($f_id)=shift;
MyUtils::delete_work($dbh,$f_id);
$dbh->commit();
$dbh->disconnect();
