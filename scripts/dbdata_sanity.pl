#!/usr/bin/perl -w

=head desciption 

This script does sanity on the database

# here is what it should check:
# 1. AUTO_INCREMENT always implies tight packing of the table.
# 2. no works without views.
# 3. no works without reviews.
# 4. no works without lenghts.
# 5. no person without single group representing him.
# 6. no works without people involved (at least one).
# 7. no person without external id of some sort (at least one).
# more to come...

Currently it does very little.

=cut

use strict;
use diagnostics;
use MP3::Info qw();
use Video::Info qw();
use IMDB::Film qw();
use File::Glob ':glob';
use DBI;

# print debug messages ?
my($debug)=0;

my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

my($sql)="select count(*) from TbWkWorkView where remark is not NULL";
my($sth)=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
$sth->finish();


# were are not making any changes using this script so there is no need to commit anything
#$dbh->commit();
$dbh->disconnect();
