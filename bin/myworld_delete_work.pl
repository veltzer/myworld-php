#!/usr/bin/perl -w

=head

This script deletes a single work using it's ID IN THE DATABASE.
Mind you: this is NOT according to imdbid.

TODO:
- print the name of the work to be deleted before it's deleted and confirm with the user.

=cut

# uses

use strict;
use diagnostics;
use DBI;
use MyImdb qw();
use MyUtils qw();

# code

if(!@ARGV) {
	die 'please provide work id'."\n";
}

my($dbh)=MyUtils::db_connect();
my($f_id)=shift;
MyUtils::delete_work($dbh,$f_id);
$dbh->commit();
$dbh->disconnect();
