#!/usr/bin/perl -w

=head desciption 

This script deletes a single movie using it's ID IN THE DATABASE.
Mind you: this is NOT according to imdbid.

=cut

use strict;
use diagnostics;
use DBI;
use MyImdb qw();
use MyUtils qw();

my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

if(!@ARGV) {
	die 'please provide f_id'."\n";
}
my($f_id)=shift;
MyUtils::delete_movie($dbh,$f_id);
$dbh->commit();
$dbh->disconnect();
