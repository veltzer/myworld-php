#!/usr/bin/perl -w

=head

This script helps you handle movies that do not have imdb ids.
You can give those movies names etc.

=cut

# uses

use strict;
use diagnostics;
use IMDB::Film qw();
use DBI qw();
use MyImdb qw();
use MyUtils qw();

# parameters

# print debug messages ?
my($debug)=0;
# print only movies with directors ?
my($only_with_directors)=0;
# print progress reports ?
my($prog)=1;
# should we skip movies for which we already have an imdbid ?
my($skip_have_imdb)=0;
# should we check for indentity of directors and if so put the imdb ?
my($do_directors)=0;

# functions

sub insert_imdbid($$) {
	my($f_id)=$_[0];
	my($imdbid)=$_[1];
	$dbh->do('INSERT INTO TbWkWorkExternal (externalCode,externalId,workId) VALUES(?,?,?)',
		undef,
		$imdbid,
		1,
		$f_id
	);
	$dbh->commit();
}

sub get_directors($) {
	my($f_id)=$_[0];
	my($sql)='SELECT TbIdPerson.firstname,TbIdPerson.surname FROM TbIdPerson,TbWkWorkContrib,TbWkWork WHERE TbWkWorkContrib.personId=TbIdPerson.id AND TbWkWorkContrib.workId=TbWkWork.id AND TbWkWork.id=\''.$f_id.'\'';
	if($debug) {
		print 'sql is ['.$sql.']'."\n";
	}
	my($sth)=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	my($rowhashref);
	my(@directors);
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_firstname)=$rowhashref->{'firstname'};
		my($f_surname)=$rowhashref->{'surname'};
		if($debug) {
			print 'f_firstname is ['.$f_firstname.']'."\n";
			print 'f_surname is ['.$f_surname.']'."\n";
		}
		my($curr_director)=$f_firstname.' '.$f_surname;
		if($debug) {
			print 'curr_director is ['.$curr_director.']'."\n";
		}
		push(@directors,$curr_director);
	}
	return join(', ',@directors);
}

sub get_views($) {
	my($f_id)=$_[0];
	my($sql)='SELECT * FROM TbWkWorkView WHERE TbWkWorkView.workId='.$f_id.'';
	if($debug) {
		print 'sql is ['.$sql.']'."\n";
	}
	my($sth)=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	my($rowhashref);
	my(@views);
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_startViewDate)=$rowhashref->{'stateViewDate'} || 'NoStartViewDate';
		my($f_endViewDate)=$rowhashref->{'endViewDate'} || 'NoEndViewDate';
		my($f_remark)=$rowhashref->{'remark'} || 'NoRemark';
		my($f_locationId)=$rowhashref->{'locationId'} || 'NoLocationId';
		my($f_deviceId)=$rowhashref->{'deviceId'} || 'NoDeviceId';
		my($f_langId)=$rowhashref->{'langId'} || 'NoLangId';
		if($debug) {
			print 'f_startViewDate is ['.$f_startViewDate.']'."\n";
			print 'f_endViewDate is ['.$f_endViewDate.']'."\n";
			print 'f_remark is ['.$f_remark.']'."\n";
			print 'f_locationId is ['.$f_locationId.']'."\n";
			print 'f_deviceId is ['.$f_deviceId.']'."\n";
			print 'f_langId is ['.$f_langId.']'."\n";
		}
		my($curr_view)=join(':',$f_startViewDate,$f_endViewDate,$f_remark,$f_locationId,$f_deviceId,$f_langId);
		if($debug) {
			print 'curr_view is ['.$curr_view.']'."\n";
		}
		push(@views,$curr_view);
	}
	return join(', ',@views);
}

sub replace_movie_name($$) {
	my($f_id)=$_[0];
	my($movie_name)=$_[1];
	$dbh->do('UPDATE TbWkWork SET name=? WHERE id=?',
		undef,
		$movie_name,
		$f_id
	);
	$dbh->commit();
}

sub my_menu() {
	return MyUtils::show_menu(
		'd - delete this movie',
		'n - give new name to this movie',
		'i - give imdbid to this movie',
		'c - put cannot find imdbid on this movie',
		'r - delete the remark',
		'q - quit',
	);
}

# code

my($dbh)=MyUtils::db_connect();

# some variables for sql
my($sql,$sth,$rowhashref);

# lets create a hash of all movie imdb ids...
my(%hashExternalCode);
$sql='SELECT externalCode,externalId,workId FROM TbWkWorkExternal,TbExternalType WHERE TbExternalType.name=\'imdb_title_id\'';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_workId)=$rowhashref->{'workId'};
	my($f_externalCode)=$rowhashref->{'externalCode'};
	$hashExternalCode{$f_workId}=$f_externalCode;
}
if($debug) {
	print 'got external codes'."\n";
}

# select all movies
$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.remark FROM TbWkWork,TbWkWorkType WHERE TbWkWork.remark IS NOT NULL AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\')';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	my($f_remark)=$rowhashref->{'remark'};
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		if(defined($f_name)) {
			print 'f_name is ['.$f_name.']'."\n";
		}
		if(defined($f_remark)) {
			print 'f_remark is ['.$f_remark.']'."\n";
		}
	}

	# lets find if we have an imdb, if we don't have one -> skip
	if(exists($hashExternalCode{$f_id}) && $skip_have_imdb) {
		if($debug) {
			print 'already have imdbid for this movie'."\n";
		}
		next;
	}

	# lets find the director from our own database, if we dont have one -> skip
	my($my_directors)=get_directors($f_id);
	if($debug) {
		print 'my_directors is ['.$my_directors.']'."\n";
	}
	if($my_directors eq '' && $only_with_directors) {
		if($debug) {
			print 'do not have director'."\n";
		}
		next;
	}
	# lets find the views associated with that movie
	my($my_views)=get_views($f_id);

	# lets find the movie over the web, if we don't get data -> skip
	my($data);
	if(defined($f_name)) {
		print 'getting internet info for ['.$f_name.']'."\n";
		$data=MyImdb::get_movie_by_title($f_name);
	}
	if(!defined($f_name) || !defined($data)) {
		if((!defined($data)) && defined($f_name)) {
			print 'didnt find movie over the web'."\n";
		}
		if(!defined($f_name)) {
			$f_name='NULL';
		}
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_name is ['.$f_name.']'."\n";
		print 'f_remark is ['.$f_remark.']'."\n";
		print 'my_directors is ['.$my_directors.']'."\n";
		print 'my_views is ['.$my_views.']'."\n";
		my($res);
		$res=my_menu();
		while($res ne 'q') {
			print 'doing ['.$res.']'."\n";
			if($res eq 'd') {
				$dbh->do('DELETE FROM TbWkWorkAuthorization WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkChapter WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkContrib WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkExternal WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkViewPerson WHERE viewId IN (SELECT id FROM TbWkWorkView WHERE workId=?)',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkView WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkReview WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWorkAlias WHERE workId=?',undef,$f_id);
				$dbh->do('DELETE FROM TbWkWork WHERE id=?',undef,$f_id);
				$dbh->commit();
			}
			if($res eq 'n') {
				my($movie_name)=MyUtils::get_from_user('give me the new movie name: ');
				replace_movie_name($f_id,$movie_name);
			}
			if($res eq 'i') {
				my($imdbid)=MyUtils::get_from_user('give me the new imdbid: ');
				insert_imdbid($f_id,$imdbid);
			}
			if($res eq 'c') {
				die('this option is not currently implemented');
			}
			if($res eq 'r') {
				$dbh->do('UPDATE TbWkWork SET remark=NULL WHERE id=?',undef,$f_id);
				$dbh->commit();
			}
			$res=my_menu();
		}
	}
	if($do_directors) {
		if($debug) {
			print 'imdb is ['.$data->{imdbID}.']'."\n";
			print 'director is ['.$data->{director}.']'."\n";
		}
		if($my_directors ne $data->{director}) {
			print 'directors are different'."\n";
			print 'director is ['.$data->{director}.']'."\n";
			print 'my_directors is ['.$my_directors.']'."\n";
			if(MyUtils::show_yes_no_dialog('give me new name for movie(y/n)? ')) {
				my($movie_name)=MyUtils::get_from_user('give me the new movie name: ');
				replace_movie_name($f_id,$movie_name);
			}
			if(MyUtils::show_yes_no_dialog('give me new imdbid(y/n)? ')) {
				my($imdbid)=MyUtils::get_from_user('give me the new imdbid: ');
				insert_imdbid($f_id,$imdbid);
			}
			if(MyUtils::show_yes_no_dialog('proceeed anyway to set imdbid(y/n)? ')) {
				# to remove the 'tt' prefix for imdb ids
				my($imdbid)=substr($data->{imdbID},2);
				insert_imdbid($f_id,$imdbid);
			}
		}
		print 'DIRECTORS ARE THE SAME!'."\n";
		# now lets commit the imdbid...
		# to remove the 'tt' prefix for imdb ids
		my($imdbid)=substr($data->{imdbID},2);
		insert_imdbid($f_id,$imdbid);
		print '============================================================'."\n";
	}
}
$dbh->disconnect();
