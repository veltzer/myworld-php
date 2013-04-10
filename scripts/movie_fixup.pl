#!/usr/bin/perl -w

use strict;
use diagnostics;
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

# stats
my($movies_saw)=0;

# connect to the database
my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
	RaiseError => 1,
	PrintWarn => 1,
	PrintError => 1,
	AutoCommit => 0,
});

# get all movies with external imdbs
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

# get all imdbs from the file
my($line);
my($movies)={};
while($line=<>) {
	chomp($line);
	if($line=~/^mark down "(.*)" imdbid "(\d{7})"$/) {
		my($f_name,$f_imdbid)=($line=~/^mark down "(.*)" imdbid "(\d{7})"$/);
		if(exists($movies->{$f_name})) {
			if($movies->{$f_name} ne $f_imdbid) {
				die('bad imdb for movie ['.$f_name.']');
			}
		} else {
			$movies->{$f_name}=$f_imdbid;
		}
		$movies_saw++;
	}
}

# get all movies without imdbs
$id_sql='SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name=\'video movie\'';
$sth=$dbh->prepare($id_sql);
$sth->execute() or die 'SQL Error: ['.$DBI::errstr.']'."\n";
while($rowhashref=$sth->fetchrow_hashref()) {
	my($f_id)=$rowhashref->{'id'};
	my($f_name)=$rowhashref->{'name'};
	if(!exists($external_hash{$f_id})) {
		if(exists($movies->{$f_name})) {
			if($do_work) {
				my($imdb)=$movies->{$f_name};
				$dbh->do('INSERT INTO TbWkWorkExternal (externalCode,externalId,workId) VALUES(?,?,?)',undef,
					$imdb,
					1,
					$f_id,
				);
				$dbh->commit();
			}
		} else {
			print 'couldnt find id for ['.$f_name.']'."\n";
		}
	}
}

if($stats) {
	print 'movies_saw is ['.$movies_saw.']'."\n";
}
if($do_work) {
	$dbh->commit();
}
$dbh->disconnect();
