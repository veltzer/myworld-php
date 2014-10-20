#!/usr/bin/perl -w

# uses

use strict;
use diagnostics;
use Net::GitHub;
use Net::GitHub::V2::Repositories;
use Config::IniFiles;

# code

my($cfg)=Config::IniFiles->new( -file => File::HomeDir->my_home.'/.githubrc' ) or die('unable to access ini file');
$param_login=$cfg->val('github', 'login');
$param_pass=$cfg->val('github', 'pass');

my($repos)=Net::GitHub::V2::Repositories->new(
	owner => $param_login,
	repo => 'nosuchrepo',
);
# list of reps to clone
my(@check_list);
# lets get the standard repositories
my($repositories)=$repos->list();
for(my($i)=0;$i<$#$repositories;$i++) {
	my($curr)=$repositories->[$i];
	my($name)=$curr->{name};
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
