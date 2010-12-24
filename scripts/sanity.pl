#!/local/tools/bin/perl -w

use strict;
use diagnostics;
use DBI;

# This script checks the sanity of my database...

# here is what it should check:
# 1. AUTO_INCREMENT always implies tight packing of the table.
# 2. no works without views.
# 3. no works without reviews.
# 4. no works without lenghts.
# 5. no person without single group representing him.
# 6. no works without people involved (at least one).
# 7. no person without external id of some sort (at least one).
# more to come...

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
my($stats)=1;
# do you want to update all lengths ?
my($do_all)=0;

# TODO: write some SQL to find the next numbers and get ridd of the hardcoded values.
my($sql);
#$sql='select * from TbWkWork';
# selecting only works which are audio (selecting all as above will NOT work...)
$sql='select TbWkWork.id,TbWkWork.name,TbWkWork.typeId from TbWkWork,TbWkWorkType where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkType.name in (\'audio book\',\'audio course\',\'audio lecture\',\'audio show\',\'video course\',\'video movie\')';
# this is just for movies
#$sql='select TbWkWork.id,TbWkWork.name,TbWkWork.typeId from TbWkWork,TbWkWorkType where TbWkWork.typeId=TbWkWorkType.id and TbWkWorkType.name in (\'video movie\')';
if($do_all) {
} else {
	#$sql.=' and ( TbWkWork.length is NULL or TbWkWork.size is NULL or TbWkWork.chapters is NULL )';
	#$sql.=' and ( TbWkWork.length is NULL )';
	$sql.=' and ( updated is false )';
}
if($debug) {
	print 'sql is ['.$sql.']'."\n";
}
# lets create a hash of all movie imdbids...
my(%external_hash);
my($id_sql)="select externalCode,workId from TbWkWorkExternal";
my($sth)=$dbh->prepare($id_sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
my($rowhashref);
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_workId)=$rowhashref->{'workId'};
	my($f_externalCode)=$rowhashref->{'externalCode'};
	$external_hash{$f_workId}=$f_externalCode;
}
my($counter)=0;
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
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
		if($type eq 'video') {
			$folder='/home/mark/links/topics_archive/video/emovies/by_title_name/'.$f_name;
		}
		if(! -d $folder) {
			die('['.$folder.'] is not a folder');
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
				my($info)=Video::Info->new(-file=>$filename);
				my($curr_secs)=$info->duration();
				my($curr_size)=$info->filesize();
				if($debug) {
					print 'curr secs is ['.$curr_secs.']'."\n";
					print 'curr size is ['.$curr_size.']'."\n";
				}
				$stat_secs+=$curr_secs;
				$stat_size+=$curr_size;
			}
		}
		my($chapters)=scalar(@file_list);
		if($prog) {
			print 'found size='.$stat_size.', secs='.$stat_secs.',chapters='.$chapters."\n";
		}
		# now update the database...
		$dbh->do('update TbWkWork set length=?,size=?,chapters=?,updated=? where id=?',undef,$stat_secs,$stat_size,scalar(@file_list),1,$f_id);
		$counter++;
	}
	if($type eq 'movie') {
		# find the imdbid of the movie
		my($imdb_id)=$external_hash{$f_id};
		#print 'imdb_id is '.$imdb_id."\n";
		#my($imdbObj)=new IMDB::Film(crit => $imdb_id, debug=>1);
		my($imdbObj)=new IMDB::Film(crit => $imdb_id);
		if($imdbObj->status) {
			my($duration);
			$duration=$imdbObj->duration();
			my($title)=$imdbObj->title();
			if($title ne $f_name) {
				die("title is $title, and name is $f_name");
			}
			if(!defined($duration)) {
				print 'movie has no duration, setting updated anyway...'."\n";
				$dbh->do('update TbWkWork set updated=? where id=?',undef,1,$f_id);
				next;
			}
			if($duration!~/^(\w+\: )?\d+ min/) {
				die("duration has a weird value $duration");
			}
			my($country,$stat_secs)=($duration=~/^(\w+\: )?(\d+) min/);
			$stat_secs*=60;
			if($prog) {
				print 'found secs='.$stat_secs."\n";
			}
			$dbh->do('update TbWkWork set length=?,updated=? where id=?',undef,$stat_secs,1,$f_id);
		} else {
			print "Something wrong: ".$imdbObj->error;
		}
	}
}
$dbh->commit();

$dbh->disconnect();

if($stats) {
	print 'did ['.$counter.'] works...'."\n";
}
