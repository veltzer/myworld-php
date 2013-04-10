#!/usr/bin/perl -w

=head desciption 

This script checks and fixes problems having to do with same movie twice inside the same
database.

=cut

use strict;
use diagnostics;
use lib 'scripts';
use DBI;
use MyImdb qw();
use MyUtils qw();

my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

# print debug messages ?
my($debug)=0;
# print progress messages ?
my($prog)=1;

die('do not use this script, it is now deprecated');

# this is for all works that this script knows how to handle which have not been updated (getting all as above will NOT work...)
my($sql)='SELECT id, externalCode, externalId, workId FROM TbWkWorkExternal';

if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets create a hash of all movie imdbids...
my($sth)=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
my($rowhashref);
my(%hash);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_externalCode)=$rowhashref->{'externalCode'};
	my($f_externalId)=$rowhashref->{'externalId'};
	my($f_workId)=$rowhashref->{'workId'};
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_externalCode is ['.$f_externalCode.']'."\n";
		print 'f_externalId is ['.$f_externalId.']'."\n";
		print 'f_workId is ['.$f_workId.']'."\n";
	}
	my($hashkey)=$f_externalId.$;.$f_externalCode;
	if(exists($hash{$hashkey})) {
		my($have_f_id,$have_f_workId)=split($;,$hash{$hashkey});
		print '========================================================='."\n";
		print 'dup f_id ['.$have_f_id.'], f_workId ['.$have_f_workId.']'."\n";
		print 'dup f_id ['.$f_id.'], f_workId ['.$f_workId.']'."\n";
		# here is what to do with dups...
		# move all views/review/authorizations of the new to the old
		$dbh->do('UPDATE TbWkWorkView set workId=? WHERE workId=?',undef,$have_f_workId,$f_workId);
		$dbh->do('UPDATE TbWkWorkReview set workId=? WHERE workId=?',undef,$have_f_workId,$f_workId);
		# remove all chapters/contribs/aliases of the new
		$dbh->do('DELETE FROM TbWkWorkAuthorization WHERE workId=?',undef,$f_workId);
		$dbh->do('DELETE FROM TbWkWorkChapter WHERE workId=?',undef,$f_workId);
		$dbh->do('DELETE FROM TbWkWorkContrib WHERE workId=?',undef,$f_workId);
		$dbh->do('DELETE FROM TbWkWorkAlias WHERE workId=?',undef,$f_workId);
		# remove the new from external
		$dbh->do('DELETE FROM TbWkWorkExternal WHERE id=?',undef,$f_id);
		# remove the new from work
		$dbh->do('DELETE FROM TbWkWork WHERE id=?',undef,$f_workId);
		$dbh->commit();
	} else {
		$hash{$hashkey}=$f_id.$;.$f_workId;
	}
}
$dbh->disconnect();
