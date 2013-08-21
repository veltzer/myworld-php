#!/usr/bin/perl -w

=head desciption 

for movies:
===========
- uses IMDB::Film to get the films duration.
- check that the name of the work is identical to the imdb name.

TODO:
- add command line usage that enabled to select whether this runs:
	- for all movies.
	- for all movies without length.
	- just shows how many movies don't have length.

=cut

use strict;
use diagnostics;
use IMDB::Film qw();
use DBI qw();
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
# print progress while working ?
my($prog)=1;
# print stats at the end ?
my($stats)=0;
# is a human watching ?
my($attended)=1;

# check title ?
my($do_check_title)=1;
# do lengths ?
my($do_duration)=1;
# do aliases ?
my($do_aliases)=1;

# the sql statement to get works that we need to work on...
my($sql);

# this is for all works that this script knows how to handle which have not been updated (getting all as above will NOT work...)
my($sql_not_updated)='SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name IN (\'video movie\') AND (updatedLengthDate IS NULL OR updatedAliasesDate IS NULL OR nameCheckedDate IS NULL)';

# this is for all works that do not have length
my($sql_no_length)='SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\') AND ( length IS NULL )';

# this is for all works (even those that were updated)...
my($sql_all)='SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'video course\',\'video movie\')';

# this is the default
$sql=$sql_not_updated;
#$sql=$sql_all;
#$sql=$sql_no_length;

if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets create a hash of all movie imdbids...
my(%external_hash);
my($id_sql)='SELECT externalCode,workId FROM TbWkWorkExternal';
my($sth)=$dbh->prepare($id_sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_workId)=$rowhashref->{'workId'};
	my($f_externalCode)=$rowhashref->{'externalCode'};
	$external_hash{$f_workId}=$f_externalCode;
}
my($counter)=0;
my($prob)=0;
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	# find the imdbid of the movie
	if(defined($external_hash{$f_id})) {
		my($imdb_id)=$external_hash{$f_id};
		# check if it's a 'could not find' imdbid...
		if($debug) {
			print 'imdb_id is '.$imdb_id."\n";
		}
		if($prog) {
			print 'doing ['.$f_name.'], ['.$f_id.']'."\n";
		}
		#my($imdbObj)=new IMDB::Film(crit => $imdb_id, debug=>1);
		my($imdbObj)=new IMDB::Film(crit => $imdb_id);
		if($imdbObj->status) {
			if($do_check_title) {
				my($title)=$imdbObj->title();
				if($title ne $f_name) {
					# old behaviour
					#die('title is ['.$title.'], name is ['.$f_name.']');
					print 'f_id is ['.$f_id.']'."\n";
					print 'f_name is ['.$f_name.']'."\n";
					print 'title is ['.$title.']'."\n";
					print 'imdb_id is ['.$imdb_id.']'."\n";
					if($attended) {
						if(MyUtils::show_yes_no_dialog('replace movie name(y/n)? ')) {
							$dbh->do('UPDATE TbWkWork SET name=? WHERE id=?',undef,$title,$f_id);
							$dbh->do('UPDATE TbWkWork SET nameCheckedDate=NOW() WHERE id=?',undef,$f_id);
							$dbh->commit();
							print 'updated movie name...'."\n";
						}
					}
				} else {
					$dbh->do('UPDATE TbWkWork SET nameCheckedDate=NOW() WHERE id=?',undef,$f_id);
					$dbh->commit();
					print 'updated movie name check time...'."\n";
				}
			}
			if($do_duration) {
				my($duration);
				$duration=$imdbObj->duration();
				if(!defined($duration)) {
					$dbh->do('UPDATE TbWkWork SET updatedLengthDate=NOW() WHERE id=?',undef,$f_id);
					$dbh->commit();
					print 'movie had no duration, updated anyway...'."\n";
				} else {
					if($duration!~/^(.*\: )?\d+ min/) {
						die('duration has a weird value ['.$duration.']');
					}
					my($country,$stat_secs)=($duration=~/^(.*\: )?(\d+) min/);
					$stat_secs*=60;
					if($debug) {
						print 'found secs='.$stat_secs."\n";
					}
					$dbh->do('UPDATE TbWkWork SET length=? WHERE id=?',undef,$stat_secs,$f_id);
					$dbh->do('UPDATE TbWkWork SET updatedLengthDate=NOW() WHERE id=?',undef,$f_id);
					$dbh->commit();
					print 'updated movie duration to ['.$stat_secs.']...'."\n";
				}
			}
			if($do_aliases) {
				$dbh->do('DELETE FROM TbWkWorkAlias WHERE workId=?',undef,$f_id);
				my($also_known_as)=$imdbObj->also_known_as();
				my($num_aliases)=$#$also_known_as+1;
				for(my($i)=0;$i<$num_aliases;$i++) {
					my($name)=$also_known_as->[$i];
					# the ne '' below is to get over imdb bugs...
					if($name ne '') {
						$dbh->do('INSERT INTO TbWkWorkAlias (workId,name) values(?,?)',undef,$f_id,$name);
					}
				}
				$dbh->do('UPDATE TbWkWork SET updatedAliasesDate=NOW() WHERE id=?',undef,$f_id);
				$dbh->commit();
				print 'updated movie with ['.$num_aliases.'] aliases...'."\n";
			}
		} else {
			print 'Could not get imdb data for imdb ['.$imdb_id.'], with error ['.$imdbObj->error.']'."\n";
		}
	} else {
		$prob++;
		if($prog) {
			if(defined($f_name)) {
				print '['.$f_name.'], ['.$f_id.'] has no imdbid'."\n";
			} else {
				print 'NULL movie name with no imdbid'."\n";
			}
		}
	}
}
$dbh->disconnect();

if($stats) {
	print 'updated ['.$counter.'] lengths...'."\n";
	print 'enountered ['.$prob.'] problems...'."\n";
}
