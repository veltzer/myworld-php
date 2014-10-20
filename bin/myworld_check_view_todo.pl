#!/usr/bin/perl -w

=head

This script helps you handle todos associated with viewing.

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

sub my_menu() {
	return MyUtils::show_menu(
		'd - set device',
		'l - set location',
		'a - add person',
		'r - delete the todo (set it to NULL)',
		'i - change the todo',
		'u - update view date to now',
		'x - delete movie entirely (DANGEROUS)',
		'q - quit',
	);
}

# code

my($dbh)=MtUtils::db_connect();

# some variables for sql
my($sql,$sth,$rowhashref);

# lets create a hash of all locations,devices and friends...
my(%hashLocation);
$sql='SELECT id,name FROM TbLocation';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_name is ['.$f_name.']'."\n";
	}
	$hashLocation{$f_id}=$f_name;
}
my(%hashDevice);
$sql='SELECT id,name FROM TbDevice';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_name is ['.$f_name.']'."\n";
	}
	$hashDevice{$f_id}=$f_name;
}
my(%hashFriend);
$sql='SELECT TbIdPerson.id,TbIdPerson.firstname,TbIdPerson.surname,TbIdPerson.remark FROM TbIdPerson,TbIdGrpPerson,TbIdGrp WHERE TbIdGrp.name=\'friends\' AND TbIdGrp.id=TbIdGrpPerson.groupId AND TbIdGrpPerson.personId=TbIdPerson.id';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_firstname)=$rowhashref->{'firstname'};
	my($f_surname)=$rowhashref->{'surname'};
	my($f_remark)=$rowhashref->{'remark'};
	my($f_name);
	if(defined($f_firstname) && defined($f_surname)) {
		$f_name=$f_firstname.' '.$f_surname;
	} else {
		if(defined($f_firstname)) {
			$f_name=$f_firstname;
		} else {
			if(defined($f_surname)) {
				$f_name=$f_surname;
			} else {
				$f_name=$f_remark;
			}
		}
	}
	if($debug) {
		print 'f_id is ['.$f_id.']'."\n";
		print 'f_name is ['.$f_name.']'."\n";
	}
	$hashFriend{$f_id}=$f_name;
}
if($debug) {
	print 'got locations,devices,friends'."\n";
}


# select all movies
$sql='SELECT TbWkWorkView.id,TbWkWork.name,TbWkWork.id as movie_id,TbWkWorkView.todo,TbWkWorkView.locationId,TbWkWorkView.deviceId FROM TbWkWork,TbWkWorkType,TbWkWorkView WHERE TbWkWorkView.todo IS NOT NULL AND TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\') AND TbWkWorkView.workId=TbWkWork.id';
$sth=$dbh->prepare($sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	my($f_movie_id)=$rowhashref->{'movie_id'};
	my($f_todo)=$rowhashref->{'todo'};
	my($f_locationId)=$rowhashref->{'locationId'};
	my($f_deviceId)=$rowhashref->{'deviceId'};
	print 'f_id is ['.$f_id.']'."\n";
	print 'f_name is ['.$f_name.']'."\n";
	print 'f_movie_id is ['.$f_movie_id.']'."\n";
	print 'f_todo is ['.$f_todo.']'."\n";
	print 'f_locationId is ['.$f_locationId.'], '.$hashLocation{$f_locationId}."\n";
	print 'f_deviceId is ['.$f_deviceId.'], '.$hashDevice{$f_deviceId}."\n";
	my($res);
	$res=my_menu();
	while($res ne 'q') {
		if($debug) {
			print 'doing ['.$res.']'."\n";
		}
		my($show_menu)=1;
		if($res eq 'd') {
			while(my($key,$val)=each(%hashDevice)) {
				print 'id is ['.$key.'], name is ['.$val.']'."\n";
			}
			my($newid)=MyUtils::get_from_user('give me the device id: ');
			$dbh->do('UPDATE TbWkWorkView SET deviceId=? WHERE id=?',undef,$newid,$f_id);
			$dbh->commit();
			$f_deviceId=$newid;
		}
		if($res eq 'l') {
			while(my($key,$val)=each(%hashLocation)) {
				print 'id is ['.$key.'], name is ['.$val.']'."\n";
			}
			my($newid)=MyUtils::get_from_user('give me the location id: ');
			$dbh->do('UPDATE TbWkWorkView SET locationId=? WHERE id=?',undef,$newid,$f_id);
			$dbh->commit();
			$f_locationId=$newid;
		}
		if($res eq 'a') {
			while(my($key,$val)=each(%hashFriend)) {
				print 'id is ['.$key.'], name is ['.$val.']'."\n";
			}
			my($newid)=MyUtils::get_from_user('give me the friend id: ');
			$dbh->do('INSERT TbWkWorkViewPerson (viewerId,viewId) VALUES(?,?)',undef,$newid,$f_id);
			$dbh->commit();
		}
		if($res eq 'r') {
			$dbh->do('UPDATE TbWkWorkView SET todo=NULL WHERE id=?',undef,$f_id);
			$dbh->commit();
		}
		if($res eq 'i') {
			my($newtodo)=MyUtils::get_from_user('give me the new todo: ');
			$dbh->do('UPDATE TbWkWorkView SET todo=? WHERE id=?',undef,$newtodo,$f_id);
			$dbh->commit();
		}
		if($res eq 'u') {
			$dbh->do('UPDATE TbWkWorkView SET endViewDate=NOW() WHERE id=?',undef,$f_id);
			$dbh->commit();
		}
		if($res eq 'x') {
			MyUtils::delete_movie($dbh,$f_movie_id);
			$dbh->commit();
			$show_menu=0;
			$res='q';
		}
		if($show_menu) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_name is ['.$f_name.']'."\n";
			print 'f_movie_id is ['.$f_movie_id.']'."\n";
			print 'f_todo is ['.$f_todo.']'."\n";
			print 'f_locationId is ['.$f_locationId.'], '.$hashLocation{$f_locationId}."\n";
			print 'f_deviceId is ['.$f_deviceId.'], '.$hashDevice{$f_deviceId}."\n";
			$res=my_menu();
		}
	}
}
$dbh->disconnect();
