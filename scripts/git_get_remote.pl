#!/usr/bin/perl -w

use strict;
use diagnostics;
use Net::GitHub;
use Net::GitHub::V2::Repositories;

# connect
my($repos)=Net::GitHub::V2::Repositories->new(
	owner => 'veltzer',
	repo => 'nosuchrepo',
);
# list of reps to clone
my(@check_list);
# lets get the standard repositories
my($repositories)=$repos->list();
for(my($i)=0;$i<$#$repositories;$i++) {
	my($curr)=$repositories->[$i];
	my($name)=$curr->{name};
	# debug code
	#while(my($key,$val)=each(%$curr)) {
	#	print $key.' => '.$val."\n";
	#}
	push(@check_list,$name);
}
# extra stuff to clone (forks)
push(@check_list,'usbutils');
# lets clone
for(my($i)=0;$i<=$#check_list;$i++) {
	my($name)=$check_list[$i];
	if(! -d $name) {
		print 'cloning ['.$name.']'."\n";
		system('git clone git@github.com:veltzer/'.$name.'.git');
	}
}
