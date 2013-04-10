#!/usr/bin/perl -w

use strict;
use diagnostics;
use XML::Simple qw();
use LWP::Simple qw();
use Data::Dumper qw();
use URI qw();
use YAML::Dumper qw();
use MyImdb qw();
use IMDB::Film qw();

my $dumper = YAML::Dumper->new;
$dumper->indent_width(4);

if(!@ARGV) {
	die 'please provide imdbid'."\n";
}
my($imdbid)=shift;
print 'fetching from imdbapi...'."\n";
my($data)=MyImdb::get_movie_by_imdbid($imdbid);
if(defined($data)) {
	print 'data is ['.$data.']'."\n";
} else {
	print 'imdbid ['.$imdbid.'] not found'."\n";
}
my($imdbObj)=new IMDB::Film(crit => $imdbid);
if($imdbObj->status) {
	my($title)=$imdbObj->title();
	#print 'title is ['.$title.']'."\n";
	#my($also_known_as)=$imdbObj->also_known_as();
	#for(my($i)=0;$i<=$#$also_known_as;$i++) {
	#	print 'also_known_as ['.$i.'] is ['.$also_known_as->[$i].']'."\n";
	#}
	#print $dumper->dump($imdbObj);
	print Data::Dumper::Dumper($imdbObj);
}
