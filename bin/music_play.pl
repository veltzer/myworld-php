#!/usr/bin/perl -w

use strict;
use diagnostics;
#use MP3::Info qw();
use MP3::Tag qw();
use DBI qw();
use File::Spec qw();

# This script plays music files and then stores their data into the database...
# TODO:
# add stuff better into the database (integrate with the works tables...).
#

##############
# PARAMETERS #
##############
# add stuff to the database ?
my($add)=1;
# print each file as it is played ?
my($print)=1;
# debug ?
my($debug)=1;
# actually play the files ?
my($play)=1;
# assert that we have idv2.4 tags?
my($assert)=0;
# where is my music?
#my($music_folder)='/home/mark/topics_archive/audio/music';
my($music_folder)='/mnt/external/mark/topics_archive/audio/music';
# check that we are only playing stuff from the music folder? This is a SECURITY CHECK, DONT REMOVE!
my($check_sec)=1;

########
# BODY #
########

# a general assertion function...
sub assert($$) {
	my($val,$msg)=@_;
	if(!$val) {
		die($msg);
	}
}
# this wraps calls to system()
sub my_system($) {
	my($cmd)=@_;
	if($debug) {
		print STDERR 'my_system ['.join(',',@{$cmd}).']'."\n";
	}
	my($res)=system(@{$cmd});
	if($debug) {
		print STDERR 'my_system res is ['.$res.']'."\n";
	}
	if($res) {
		die('error in system ['.join(',',@{$cmd}).'], result is ['.$res.']');
	}
	#return $res;
}
# function to return the current time in mysql format
sub mysql_now() {
	my($sec,$min,$hour,$mday,$mon,$year,$wday, $yday,$isdst)=localtime(time);
	my($result)=sprintf('%4d-%02d-%02d %02d:%02d:%02d',$year+1900,$mon+1,$mday,$hour,$min,$sec);
	return $result;
}

for(my($i)=0;$i<@ARGV;$i++) {
	my($filename)=$ARGV[$i];
	my($absfile)=File::Spec->rel2abs($filename);
	if($check_sec) {
		if($absfile!~/^$music_folder/) {
			die('cannot play out of music folder music');
		}
	}
	my($date)=mysql_now();
	if($print) {
		print STDERR 'playing ['.$filename.']...'."\n";
	}
	# check that you can get all the mp3 info from the file...
	my($mp3)=MP3::Tag->new($filename);
	# only use ID3v2
	$mp3->config("autoinfo","ID3v2");
	$mp3->get_tags();
	if(!exists($mp3->{'ID3v2'})) {
		die('do not have tag info on the file');
	}
	my($title)=$mp3->title();
	my($track)=$mp3->track();
	my($artist)=$mp3->artist();
	my($album)=$mp3->album();
	my($comment)=$mp3->comment();
	my($year)=$mp3->year();
	my($genre)=$mp3->genre();
	#my($title, $track, $artist, $album, $comment, $year, $genre) = $mp3->autoinfo();
	if($debug) {
		#my($res)=MP3::Info::get_mp3info($filename);
		#while(my($key,$value)=each(%$res)) {
		#	print $key.' -> '.$value."\n";
		#}
		my(@tags)=$mp3->get_tags();
		print join('-',@tags)."\n";
	}
	if($print) {
		print STDERR '================= info going into the database ==============='."\n";
		print STDERR 'title is '.$title."\n";
		print STDERR 'track is '.$track."\n";
		print STDERR 'artist is '.$artist."\n";
		print STDERR 'album is '.$album."\n";
		print STDERR 'comment is '.$comment."\n";
		print STDERR 'year is '.$year."\n";
		print STDERR 'genre is '.$genre."\n";
		print STDERR 'absfile is '.$absfile ."\n";
		print STDERR 'date is '.$date."\n";
		print STDERR '=============================================================='."\n";
	}
	if($assert) {
		assert($title ne '','have no title');
		assert($track ne '','have no track');
		assert($artist ne '','have no artist');
		assert($album ne '','have no album');
		# we do not assert comment...
		#assert($comment ne '','have no comment');
		assert($year ne '','have no year');
		assert($genre ne '','have no genre');
	}
	if($play) {
		my_system(['mplayer',$filename]);
	}
	# we only add to the database once the playing is over...
	if($add) {
		my($dbh)=DBI->connect('dbi:mysql:myworld','','',{
				RaiseError=>1,
				PrintWarn=>1,
				PrintError=>1,
				AutoCommit=>0,
			});
		$dbh->do('INSERT INTO TbMsHearing (title,track,artist,album,comment,year,genre,filename,date) VALUES(?,?,?,?,?,?,?,?,?)',undef,
			$title,
			$track,
			$artist,
			$album,
			$comment,
			$year,
			$genre,
			$absfile,
			$date,
		);
		$dbh->commit();
		$dbh->disconnect();
	}
}
