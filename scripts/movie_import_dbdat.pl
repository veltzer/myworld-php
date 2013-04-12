#!/usr/bin/perl -w

use strict;
use diagnostics;
use DBI;
use XML::Twig;
use Date::Parse;
use Date::Manip;

# this is the old import script for my movies
#
# In order to know that I am importing everything I use:
# xmlutils_stats.pl --file=movie_dbdat.xml

# here is the entire file structure:
#[authorization] appeared [15] times
#[person] appeared [824] times
#[title] appeared [1735] times
#[person_title_role] appeared [1657] times
#[title_authorization] appeared [2234] times
#[person_title] appeared [2752] times
#[review] appeared [79] times

# stuff which was handled:
# authorization
# person
# title
# person_title_role
# title_authorization
# person_title
# review
#	in review the score needs to be adjusted (multiplied by 2).

# TODO:
# check that everything is done well.
# find out how to backup the database and document it here.
# 		mysqldump myworld | mysql myworld_test
# backup the database and run this...

# parameters start here
########################
# print debug messages ?
my($debug)=1;
# do database stuff ?
my($do_db)=1;
# do commit ?
my($do_commit)=1;

=head
sub to_mysql($) {
	my($date_string)=$_[0];
	print $date_string;
	my($time);
	$time=str2time($date_string);
	print $time;
	return "NULL";
}
=cut

sub to_mysql($) {
	my($string)=@_;
	my($object)=Date::Manip::UnixDate($string,'%Y-%m-%d %T');
	if(!defined($object)) {
		print "string: $string\n";
	}
	return($object);
}

my($dbh);
#my($person_sql)='select id from TbExternalType where name=\'imdb_person\'';
my($imdb_person_code)=42;
my($imdb_title_code)=1;

# [authorization.name] appeared [15] times
# [authorization.xmlid] appeared [15] times
# [authorization.description] appeared [15] times
my(%authorizations);
sub handle_authorization() {
	my($t,$el)=@_;
	my($name)=$el->{'att'}->{'name'};
	my($xmlid)=$el->{'att'}->{'xmlid'};
	my($description)=$el->{'att'}->{'description'};
	if($debug) {
		print "authorization: name is $name\n";
		print "authorization: xmlid is $xmlid\n";
		print "authorization: description is $description\n";
	}
	if($do_db) {
		$dbh->do('INSERT INTO TbAuthorization (name,description) VALUES(?,?)',undef,$name,$description);
		my($id)=$dbh->last_insert_id(undef,undef,undef,undef);
		$authorizations{$xmlid}=$id;
		if($debug) {
			print "authorization: id is $id\n";
		}
	}
}
# [person.firstname] appeared [823] times
# [person.xmlid] appeared [824] times
# [person.surname] appeared [824] times
# [person.imdbid] appeared [726] times
# [person.lineage] appeared [58] times
# [person.sequential] appeared [5] times
my(%persons);
sub handle_person() {
	my($t,$el)=@_;
	# get the data
	my($xmlid)=$el->{'att'}->{'xmlid'};
	my($firstname)=$el->{'att'}->{'firstname'};
	my($surname)=$el->{'att'}->{'surname'};
	my($lineage)=$el->{'att'}->{'lineage'};
	my($imdbid)=$el->{'att'}->{'imdbid'};
	my($sequential)=$el->{'att'}->{'sequential'};
	# debug
	if($debug) {
		if(defined($xmlid)) {
			print "person: xmlid is [$xmlid]\n";
		}
		if(defined($firstname)) {
			print "person: firstname is [$firstname]\n";
		}
		if(defined($surname)) {
			print "person: surname is [$surname]\n";
		}
		if(defined($lineage)) {
			print "person: lineage is [$lineage]\n";
		}
		if(defined($imdbid)) {
			print "person: imdbid is [$imdbid]\n";
		}
		if(defined($sequential)) {
			print "person: sequential is [$sequential]\n";
		}
	}
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbIdPerson (firstname,surname,othername,ordinal) VALUES(?,?,?,?)',undef,
			$firstname,
			$surname,
			$lineage,
			$sequential,
		);
		my($personid)=$dbh->last_insert_id(undef,undef,undef,undef);
		$persons{$xmlid}=$personid;
		if(defined($imdbid)) {
			$dbh->do('INSERT INTO TbIdPersonExternal (externalCode,externalId,personId) VALUES(?,?,?)',undef,
				$imdbid,
				$imdb_person_code,
				$personid,
			);
			#my($externalid)=$dbh->last_insert_id(undef,undef,undef,undef);
		}
		if($debug) {
			print "person: personid is $personid\n";
		}
	}
}
# [title.xmlid] appeared [1735] times
# [title.description] appeared [62] times
# [title.imdbid] appeared [89] times
# [title.name] appeared [1674] times
# [title.tickets] appeared [66] times
my(%titles);
sub handle_title() {
	my($t,$el)=@_;
	# get the data
	my($xmlid)=$el->{'att'}->{'xmlid'};
	my($name)=$el->{'att'}->{'name'};
	my($description)=$el->{'att'}->{'description'};
	my($imdbid)=$el->{'att'}->{'imdbid'};
	my($tickets)=$el->{'att'}->{'tickets'};
	if(!defined($name)) {
		$name="NULL";
	}
	if(!defined($description)) {
		$description="NULL";
	}
	if(!defined($tickets)) {
		$tickets="NULL";
	}
	if($debug) {
		print "title: xmlid is [$xmlid]\n";
		print "title: name is [$name]\n";
		print "title: description is [$description]\n";
		print "title: imdbid is [$imdbid]\n";
		print "title: tickets is [$tickets]\n";
	}
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbWkWork (name,description,tickets,typeId) VALUES(?,?,?,?)',undef,
			$name,
			$description,
			$tickets,
			15,
		);
		my($titleid)=$dbh->last_insert_id(undef,undef,undef,undef);
		$titles{$xmlid}=$titleid;
		if(defined($imdbid)) {
			$dbh->do('INSERT INTO TbWkWorkExternal (externalCode,externalId,workId) VALUES(?,?,?)',undef,
				$imdbid,
				$imdb_title_code,
				$titleid,
			);
			#my($externalid)=$dbh->last_insert_id(undef,undef,undef,undef);
		}
	}
}
# [person_title_role.person_id] appeared [1657] times
# [person_title_role.role_id] appeared [1657] times
# [person_title_role.title_id] appeared [1657] times
sub handle_person_title_role() {
	my($t,$el)=@_;
	# get the data
	my($f_person_id)=$el->{'att'}->{'person_id'};
	my($f_role_id)=$el->{'att'}->{'role_id'};
	my($f_title_id)=$el->{'att'}->{'title_id'};
	# translate into my own ids
	my($db_person_id)=$persons{$f_person_id};
	my($db_role_id)=6; # always director
	my($db_title_id)=$titles{$f_title_id};
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbWkWorkContrib (workId,personId,typeId) VALUES(?,?,?)',undef,
			$db_title_id,
			$db_person_id,
			$db_role_id,
		);
	}
}
# [title_authorization.authorization_id] appeared [2234] times
# [title_authorization.title_id] appeared [2234] times
sub handle_title_authorization() {
	my($t,$el)=@_;
	# get the data
	my($f_authorization_id)=$el->{'att'}->{'authorization_id'};
	my($f_title_id)=$el->{'att'}->{'title_id'};
	# translate into my own ids
	my($db_authorization_id)=$authorizations{$f_authorization_id};
	my($db_title_id)=$titles{$f_title_id};
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbWkWorkAuthorization (workId,authorizationId) VALUES(?,?)',undef,
			$db_title_id,
			$db_authorization_id,
		);
	}
}
# [person_title.person_id] appeared [2752] times
# [person_title.title_id] appeared [2752] times
# [person_title.remark] appeared [269] times
# [person_title.date] appeared [481] times
sub handle_person_title() {
	my($t,$el)=@_;
	my($f_person_id)=$el->{'att'}->{'person_id'};
	my($f_title_id)=$el->{'att'}->{'title_id'};
	# date and remark may not exist!!! Handle this.
	my($f_remark)=$el->{'att'}->{'remark'};
	if(!defined($f_remark)) {
		$f_remark="NULL";
	}
	my($f_date)=$el->{'att'}->{'date'};
	# translate
	my($db_title_id)=$titles{$f_title_id};
	my($db_person_id)=$persons{$f_person_id};
	my($db_date);
	if(!defined($f_date)) {
		$db_date="NULL";
	} else {
		$db_date=to_mysql($f_date);
	}
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbWkWorkView (endViewDate,workId,remark,deviceId,locationId) VALUES(?,?,?,?,?)',undef,
			$db_date,
			$db_title_id,
			$f_remark,
			11,
			10,
		);
		my($externalid)=$dbh->last_insert_id(undef,undef,undef,undef);
		$dbh->do('INSERT INTO TbWkWorkViewPerson (viewId,viewerId) VALUES(?,?)',undef,
			$externalid,
			$db_person_id,
		);
	}
	#if($debug) {
	#	print "db_date (view): [$db_date]\n";
	#}
}
# [review.text] appeared [79] times
# [review.person_id] appeared [79] times
# [review.date] appeared [79] times
# [review.score] appeared [79] times
# [review.title_id] appeared [79] times
sub handle_review() {
	my($t,$el)=@_;
	my($f_review_text)=$el->{'att'}->{'text'};
	my($f_person_id)=$el->{'att'}->{'person_id'};
	my($f_date)=$el->{'att'}->{'date'};
	my($f_score)=$el->{'att'}->{'score'};
	$f_score=$f_score*2;
	my($f_title_id)=$el->{'att'}->{'title_id'};
	my($db_title_id)=$titles{$f_title_id};
	my($db_person_id)=$persons{$f_person_id};
	my($db_date);
	if(!defined($f_date)) {
		$db_date="NULL";
	} else {
		$db_date=to_mysql($f_date);
	}
	# INSERT INTO the database
	if($do_db) {
		$dbh->do('INSERT INTO TbWkWorkReview (ratingId,review,reviewDate,workId,reviewerId) VALUES(?,?,?,?,?)',undef,
			$f_score,
			$f_review_text,
			$db_date,
			$db_title_id,
			$db_person_id,
		);
	}
	#if($debug) {
	#	print "db_date (review): [$db_date]\n";
	#}
}

# here is the real code

if($do_db) {
	$dbh=DBI->connect('dbi:mysql:myworld','','',{
		RaiseError => 1,
		PrintWarn => 1,
		PrintError => 1,
		AutoCommit => 0,
	});
}
my($t)=XML::Twig->new(twig_handlers => {
	authorization => \&handle_authorization,
	person => \&handle_person,
	title => \&handle_title,
	person_title_role => \&handle_person_title_role,
	title_authorization => \&handle_title_authorization,
	person_title => \&handle_person_title,
	review => \&handle_review,
});
$t->parsefile('movie_dbdat.xml');
if($do_db) {
	if($do_commit) {
		$dbh->commit();
	}
	$dbh->disconnect();
}
