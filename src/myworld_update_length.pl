#!/usr/bin/perl -w

=head

the script updates various bits of information in the database.
it goes to the database and iterates all works that have not been updated.

for audio:
- it determines the folder where the work is and summs the length of all
mp3s, the size and number of chapters in that folder writing the data back to the database
using an update.
- it inserts all the chapters into the database.

for video:
- same as audio but determines length via the Video::Info module.

for movies:
- uses IMDB::Film to get the films duration.
- check that the name of the work is identical to the imdb name.

hints:
- use this query to see all types of works in my database:
SELECT DISTINCT(TbWkWork.typeId) FROM TbWkWork;
- use this query to all chapters of a certain work:
	TBD

TODO:
- make this script be able to handle all titles and just new title by a command line switch.

=cut

# uses

use strict;
use diagnostics;
use MP3::Info qw();
use Video::Info qw();
use IMDB::Film qw();
use File::Glob ':glob';
use DBI;
use MyUtils qw();
use MyVideo qw();

# parameters

# print debug messages ?
my($debug)=0;
# print progress while working ?
my($prog)=1;
# print stats at the end ?
my($stats)=1;
# clean chapters ?
my($clean_chapters)=0;
# insert chapters ?
my($do_chapters)=1;
# do lengths ?
my($do_length)=1;
# do video?
my($do_video)=1;

# code

my($dbh)=MyUtils::db_connect();
# the sql statement to get works that we need to work on...
my($sql);

# this is for all works
#$sql='SELECT * FROM TbWkWork';

# this is for all works that this script knows how to handle which have not been updated (getting all as above will NOT work...)
$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.typeId FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'video course\') AND ( updatedLengthDate IS NULL )';

# this is for all works that do not have length
#$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.typeId FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'video course\',\'video movie\') AND ( length IS NULL )';

# this is for all works (even those that were updated)...
#$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.typeId FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'video course\',\'video movie\')';

# this is just for movies
#$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.typeId FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\')';

# this is just for non updated movies
#$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.typeId FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\') AND ( updatedLengthDate IS NULL)';

if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets clean chapters if need be...
if($clean_chapters) {
	# remove all chapters...
	$dbh->do('DELETE FROM TbWkWorkChapter',undef);
	# set the auto increment for the ids to start at 1...
	$dbh->do('ALTER TABLE TbWkWorkChapter AUTO_INCREMENT=1',undef);
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
my($stat_update)=0;
my($stat_count)=0;
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	$stat_update++;
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	my($f_typeId)=$rowhashref->{'typeId'};
	my($type);
	if($f_typeId==2 || $f_typeId==3 || $f_typeId==4 || $f_typeId==5) {
		$type='audio';
	}
	if($f_typeId==13) {
		$type='video';
	}
	if($f_typeId==15) {
		$type='movie';
	}
	if($prog) {
		print 'doing ['.$f_name.']'."\n";
	}
	if($type eq 'audio' || $type eq 'video') {
		my($folder);
		if($type eq 'audio') {
			$folder='/home/mark/links/topics_archive/audio/abooks/by_title_name/'.$f_name;
		}
		if($do_video==0 && $type eq 'video') {
			print('breaking since this is a video'."\n");
			next;
		}
		if($type eq 'video') {
			$folder='/home/mark/links/topics_archive/video/emovies/by_title_name/'.$f_name;
		}
		if(! -d $folder) {
			#die('['.$folder.'] is not a folder'."\n");
			print('['.$folder.'] is not a folder'."\n");
			next;
		}
		# now calculate the length
		my(@file_list)=<$folder/*>;
		my($stat_secs)=0;
		my($stat_size)=0.0;
		for(my($i)=0;$i<@file_list;$i++) {
			my($filename)=$file_list[$i];
			if($debug) {
				print $filename."\n";
			}
			if($type eq 'audio') {
				my($res)=MP3::Info::get_mp3info($filename);
				if($debug) {
					while(my($key,$value)=each(%$res)) {
						print $key.' -> '.$value."\n";
					}
				}
				if(!exists($res->{'SECS'})) {
					die('have not found SECS for ['.$filename.']');
				}
				if(!exists($res->{'SIZE'})) {
					die('have not found SIZE for ['.$filename.']');
				}
				$stat_secs+=$res->{'SECS'};
				$stat_size+=$res->{'SIZE'};
			}
			if($type eq 'video') {
				#my($info)=Video::Info->new(-file=>$filename);
				#my($curr_secs)=$info->duration();
				#my($curr_size)=$info->filesize();
				my(%info)=MyVideo::info($filename);
				my($curr_secs)=$info{'durationsecs'};
				my($curr_size)=$info{'size'};
				if($debug) {
					print 'curr secs is ['.$curr_secs.']'."\n";
					print 'curr size is ['.$curr_size.']'."\n";
				}
				$stat_secs+=$curr_secs;
				$stat_size+=$curr_size;
			}
			if($do_chapters) {
				$dbh->do('INSERT INTO TbWkWorkChapter (ordinal,name,workId) VALUES(?,?,?)',
					undef,
					$i+1,
					$filename,
					$f_id
				);
			}
		}
		my($chapters)=scalar(@file_list);
		if($prog) {
			print 'found size='.$stat_size.', secs='.$stat_secs.',chapters='.$chapters."\n";
		}
		if($do_length) {
			$dbh->do('UPDATE TbWkWork SET length=?,size=?,chapters=?,updatedLengthDate=NOW() WHERE id=?',undef,
				$stat_secs,
				$stat_size,
				scalar(@file_list),
				$f_id
			);
		}
		$stat_update++;
	}
}
$dbh->commit();
$dbh->disconnect();

if($stats) {
	print 'stat_count ['.$stat_count.']'."\n";
	print 'stat_update ['.$stat_update.']'."\n";
}
