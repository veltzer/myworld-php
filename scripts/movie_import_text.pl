#!/usr/bin/perl -w

use strict;
use diagnostics;
use lib 'scripts';
use MyImdb qw();
use MyUtils qw();
use DBI qw();

# print debug messages ?
my($debug)=0;
# print progress reports ?
my($prog)=1;
# print stats ?
my($stats)=1;
# do actual inserting ?
my($do_work)=1;

sub get_movies();
sub get_movies_reviews();
sub get_devices();
sub get_friends();
sub get_locations();

die("do not use me! I'm already imported");

# connect to the database
my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});
my($locations)=get_locations();
my($devices)=get_devices();
my($friends)=get_friends();
my($movieNameByImdb);
my($movieIdByImdb);
my($movieWithReviews);
my($movieRatings);
get_movies();
get_movies_reviews();
my($have_movie)=0;
my($dont_have_movie)=0;

sub get_movies() {
	my($sql,$sth,$rowhashref);
	$movieNameByImdb={};
	$movieIdByImdb={};
	$sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWorkExternal.externalCode FROM TbWkWork,TbWkWorkType,TbWkWorkExternal,TbExternalType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\' AND TbWkWorkExternal.workId=TbWkWork.id AND TbWkWorkExternal.externalId=TbExternalType.id and TbExternalType.name=\'imdb_title\'';
	$sth=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_id)=$rowhashref->{'id'};
		my($f_name)=$rowhashref->{'name'};
		my($f_externalCode)=$rowhashref->{'externalCode'};
		if($debug) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_name is ['.$f_name.']'."\n";
			print 'f_externalCode is ['.$f_externalCode.']'."\n";
		}
		$movieNameByImdb->{$f_externalCode}=$f_name;
		$movieIdByImdb->{$f_externalCode}=$f_id;
	}
}

sub get_movies_reviews() {
	my($sql,$sth,$rowhashref);
	$movieWithReviews={};
	$movieRatings={};
	$sql='SELECT TbWkWork.id,TbWkWorkReview.review,TbWkWorkReview.ratingId FROM TbWkWork,TbWkWorkReview WHERE TbWkWork.id=TbWkWorkReview.workId';
	$sth=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_id)=$rowhashref->{'id'};
		my($f_review)=$rowhashref->{'review'};
		my($f_ratingId)=$rowhashref->{'ratingId'};
		if($debug) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_review is ['.$f_review.']'."\n";
			print 'f_ratingId is ['.$f_ratingId.']'."\n";
		}
		$movieWithReviews->{$f_id}=$f_review;
		$movieRatings->{$f_id}=$f_ratingId;
	}
}

sub get_friends() {
	my($sql,$sth,$rowhashref);
	my($friends)={};
	$sql='SELECT TbIdPerson.id,TbIdPerson.firstname,TbIdPerson.surname,TbIdPerson.ordinal,TbIdPerson.remark FROM TbIdPerson,TbIdGrpPerson,TbIdGrp WHERE TbIdGrp.name=\'friends\' AND TbIdGrp.id=TbIdGrpPerson.groupId AND TbIdGrpPerson.personId=TbIdPerson.id';
	$sth=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_id)=$rowhashref->{'id'};
		my($f_firstname)=$rowhashref->{'firstname'};
		my($f_surname)=$rowhashref->{'surname'};
		my($f_ordinal)=$rowhashref->{'ordinal'};
		if($debug) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_firstname is ['.$f_firstname.']'."\n";
			print 'f_surname is ['.$f_surname.']'."\n";
			print 'f_ordinal is ['.$f_ordinal.']'."\n";
		}
		my(@mylist);
		if(defined($f_firstname)) {
			push(@mylist,$f_firstname);
		}
		if(defined($f_surname)) {
			push(@mylist,$f_surname);
		}
		if(defined($f_ordinal)) {
			push(@mylist,$f_ordinal);
		}
		my($name)=join(' ',@mylist);
		if(exists($friends->{$name})) {
			die('duplicate ['.$name.']');
		}
		$friends->{$name}=$f_id;
	}
	return $friends;
}

sub get_locations() {
	my($sql,$sth,$rowhashref);
	my($locations)={};
	$sql='SELECT id,slug FROM TbLocation';
	$sth=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_id)=$rowhashref->{'id'};
		my($f_slug)=$rowhashref->{'slug'};
		if($debug) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_slug is ['.$f_slug.']'."\n";
		}
		$locations->{$f_slug}=$f_id;
	}
	return $locations;
}

sub get_devices() {
	my($sql,$sth,$rowhashref);
	my($devices)={};
	$sql='SELECT id,slug FROM TbDevice';
	$sth=$dbh->prepare($sql);
	$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($f_id)=$rowhashref->{'id'};
		my($f_slug)=$rowhashref->{'slug'};
		if($debug) {
			print 'f_id is ['.$f_id.']'."\n";
			print 'f_slug is ['.$f_slug.']'."\n";
		}
		$devices->{$f_slug}=$f_id;
	}
	return $devices;
}

sub store_it($) {
	my($hash)=$_[0];
	if($debug) {
		while(my($key,$val)=each(%$hash)) {
			print $key.','.$val."\n";
		}
		print '================================================='."\n";
	}
	my($f_id);
	if(exists($movieNameByImdb->{$hash->{imdbid}})) {
		my($have_name)=$movieNameByImdb->{$hash->{imdbid}};
		if($have_name ne $hash->{name}) {
			die('bad name ['.$hash->{name}.'], imdbid ['.$hash->{imdbid}.'], have_name ['.$have_name.']');
		} else {
			$f_id=$movieIdByImdb->{$hash->{imdbid}};
			if(exists($hash->{review}) || exists($hash->{rating})) {
				if(exists($movieWithReviews->{$f_id})) {
					print 'review is ['.$movieWithReviews->{$f_id}.']'."\n";
					print 'ratingId is ['.$movieRatings->{$f_id}.']'."\n";
					die('have review for movie already in db ['.$hash->{name}.'], imdbid ['.$hash->{imdbid}.'], have_name ['.$have_name.']');
				}
			}
			if($hash->{tickets}) {
				if($do_work) {
					$dbh->do('UPDATE TbWkWork SET tickets=tickets+? WHERE id=?',undef,$hash->{tickets},$f_id);
				}
			}
			$have_movie++;
		}
	} else {
		$dont_have_movie++;
		if($do_work) {
			if($hash->{tickets}) {
				$dbh->do('INSERT INTO TbWkWork (name,typeId,tickets) VALUES(?,?,?)',undef,$hash->{name},15,$hash->{tickets});
				$f_id=$dbh->last_insert_id(undef,undef,undef,undef);
			} else {
				$dbh->do('INSERT INTO TbWkWork (name,typeId) VALUES(?,?)',undef,$hash->{name},15);
				$f_id=$dbh->last_insert_id(undef,undef,undef,undef);
			}
		} else {
			$f_id='fake value';
		}
	}
	# now I have $f_id
	if(!exists($hash->{date})) {
		$hash->{date}=undef;
	}
	if(!exists($hash->{remark})) {
		$hash->{remark}=undef;
	}
	if(!exists($hash->{device})) {
		die('dont have device for ['.$hash->{name}.']');
	}
	if(!exists($hash->{location})) {
		die('dont have location for ['.$hash->{name}.']');
	}
	my($view_id);
	if($do_work) {
		$dbh->do('INSERT INTO TbWkWorkView (endViewDate,workId,remark,deviceId,locationId) VALUES(?,?,?,?,?)',undef,
			$hash->{date},
			$f_id,
			$hash->{remark},
			$hash->{device},
			$hash->{location},
		);
		$view_id=$dbh->last_insert_id(undef,undef,undef,undef);
		$dbh->do('INSERT INTO TbWkWorkViewPerson (viewId,viewerId) VALUES(?,?)',undef,
			$view_id,
			1,
		);
	}
	# insert the with people
	if(exists($hash->{with})) {
		my($arr)=$hash->{with};
		for(my($i)=0;$i<scalar(@$arr);$i++) {
			my($curr)=$arr->[$i];
			#print 'doing with ['.$curr.'] for ['.$hash->{name}.']'."\n";
			if($do_work) {
				$dbh->do('INSERT INTO TbWkWorkViewPerson (viewId,viewerId) VALUES(?,?)',undef,
					$view_id,
					$curr,
				);
			}
		}
	}
	if(exists($hash->{rating})) {
		if(!exists($hash->{review})) {
			die('got rating without review for ['.$f_id.'], name ['.$hash->{name}.'] imdbid ['.$hash->{imdbid}.']');
		}
		if(!defined($hash->{date})) {
			die('got bad date for ['.$f_id.'], name ['.$hash->{name}.'] imdbid ['.$hash->{imdbid}.']');
		}
		if($do_work) {
			$dbh->do('INSERT INTO TbWkWorkReview (ratingId,review,reviewDate,workId,reviewerId) VALUES(?,?,?,?,?)',undef,
				$hash->{rating},
				$hash->{review},
				$hash->{date},
				$f_id,
				1,
			);
		}
	}
}

my($line);
my($hash)={};
$hash->{with}=[];
my($started)=0;
while($line=<>) {
	my($handled)=0;
	chomp($line);
	if($line=~/^mark down "(.*)" imdbid "(\d{7})"$/) {
		if($started) {
			store_it($hash);
		}
		$hash={};
		$hash->{with}=[];
		my($f_name,$f_imdbid)=($line=~/^mark down "(.*)" imdbid "(\d{7})"$/);
		$hash->{name}=$f_name;
		$hash->{imdbid}=$f_imdbid;
		$started=1;
		$handled=1;
	}
	if($line=~/^\ttickets: (.*)$/) {
		my($f_tickets)=($line=~/^\ttickets: (.*)$/);
		$hash->{tickets}=$f_tickets;
		$handled=1;
	}
	# view fields
	if($line=~/^\tdate: (.*)$/) {
		my($f_date)=($line=~/^\tdate: (.*)$/);
		$f_date=MyUtils::to_mysql($f_date);
		$hash->{date}=$f_date;
		$handled=1;
	}
	if($line=~/^\tlocation: (.*)$/) {
		my($f_location)=($line=~/^\tlocation: (.*)$/);
		if(!exists($locations->{$f_location})) {
			die('bad location ['.$f_location.']');
		}
		$f_location=$locations->{$f_location};
		$hash->{location}=$f_location;
		$handled=1;
	}
	if($line=~/^\tdevice: (.*)$/) {
		my($f_device)=($line=~/^\tdevice: (.*)$/);
		if(!exists($devices->{$f_device})) {
			die('bad device ['.$f_device.']');
		}
		$f_device=$devices->{$f_device};
		$hash->{device}=$f_device;
		$handled=1;
	}
	if($line=~/^\twith: (.*)$/) {
		my($f_with)=($line=~/^\twith: (.*)$/);
		if(!exists($friends->{$f_with})) {
			die('bad friend ['.$f_with.']');
		}
		$f_with=$friends->{$f_with};
		push($hash->{with},$f_with);
		$handled=1;
	}
	if($line=~/^\tremark: (.*)$/) {
		my($f_remark)=($line=~/^\tremark: (.*)$/);
		$hash->{remark}=$f_remark;
		$handled=1;
	}
	# review fields
	if($line=~/^\trating: (\d)$/) {
		my($f_rating)=($line=~/^\trating: (\d)$/);
		$hash->{rating}=$f_rating;
		$handled=1;
	}
	if($line=~/^\treview: (.*)$/) {
		my($f_review)=($line=~/^\treview: (.*)$/);
		$hash->{review}=$f_review;
		$handled=1;
	}
	# if the line is bizzare...
	if($handled eq 0) {
		die('bad line ['.$line.']');
	}
}
store_it($hash);
if($stats) {
	print 'have_movie ['.$have_movie.']'."\n";
	print 'dont_have_movie ['.$dont_have_movie.']'."\n";
}
if($do_work) {
	$dbh->commit();
}
$dbh->disconnect();
