#!/usr/bin/perl -w

=head

This script checks the names of the movies that have imdbs.

=cut

use strict;
use diagnostics;
#use MP3::Info qw();
#use Video::Info qw();
#use File::Glob ':glob';
use IMDB::Film qw();
use DBI;
use MyImdb qw();
use MyUtils qw();

# parameters

# print debug messages ?
my($debug)=0;
# print progress messages ?
my($prog)=1;

# code

die('do not run this script, it is deprecated...');

my($dbh)=MyUtils::db_connect();
# this is for all works that this script knows how to handle which have not been updated (getting all as above will NOT work...)
my($sql_all)='SELECT TbWkWork.id,TbWkWork.name,TbWkWorkExternal.externalCode FROM TbWkWork,TbWkWorkType,TbWkWorkExternal,TbExternalType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkExternal.workId=TbWkWork.id AND TbWkWorkExternal.externalId=TbExternalType.id and TbExternalType.name=\'imdb_title_id\'';
my($sql_date_is_null)='SELECT TbWkWork.id,TbWkWork.name,TbWkWorkExternal.externalCode FROM TbWkWork,TbWkWorkType,TbWkWorkExternal,TbExternalType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkExternal.workId=TbWkWork.id AND TbWkWorkExternal.externalId=TbExternalType.id and TbExternalType.name=\'imdb_title_id\' AND TbWkWork.nameCheckedDate IS NULL';

# the sql statement to get works that we need to work on...
my($sql)=$sql_date_is_null;

if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets create a hash of all movie imdbids...
my($sth)=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	my($f_externalCode)=$rowhashref->{'externalCode'};
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_name is ['.$f_name.']'."\n";
		print 'f_externalCode is ['.$f_externalCode.']'."\n";
	}
	if($prog) {
		print 'fetching ['.$f_name.']'."\n";
	}
	my($imdbObj)=new IMDB::Film(crit => $f_externalCode);
	if($imdbObj->status) {
		my($title)=$imdbObj->title();
		if($title ne $f_name) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_name is ['.$f_name.']'."\n";
			print 'f_externalCode is ['.$f_externalCode.']'."\n";
			print('bad match title is ['.$title.'], f_name is ['.$f_name.'], f_id is ['.$f_id.'], f_externalCode is ['.$f_externalCode.']'."\n");
			if(MyUtils::show_yes_no_dialog('replace movie name(y/n)? ')) {
				$dbh->do('UPDATE TbWkWork SET name=? WHERE id=?',undef,$title,$f_id);
				$dbh->commit();
				print 'updated movie name...'."\n";
			}
		} else {
			if($prog) {
				print 'good match'."\n";
			}
			$dbh->do('UPDATE TbWkWork SET nameCheckedDate=NOW() WHERE id=?',undef,$f_id);
			$dbh->commit();
		}
	} else {
		die('havent found move data for imdbid ['.$f_externalCode.']');
	}
}
$dbh->commit();
$dbh->disconnect();
