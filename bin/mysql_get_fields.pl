#!/usr/bin/perl -w

=head

A script that prints the names of the columns in a table

=cut

# uses

use strict;
use diagnostics;
use DBI;
use Data::Dumper;
use Getopt::Long;
use Config::IniFiles;
use File::HomeDir;

# parameters

my($debug)=1;

# code

sub db_connect($) {
	my($rcfile)=@_;
	my($cfg);
	$cfg=Config::IniFiles->new( -file => $rcfile ) || die('unable to access ini file '.$rcfile);
	my($param_user)=$cfg->val('db', 'user');
	my($param_pass)=$cfg->val('db', 'pass');
	my($param_host)=$cfg->val('db', 'host');
	my($param_port)=$cfg->val('db', 'port');
	my($param_name)=$cfg->val('db', 'name');

	my($dsn)='dbi:mysql:'.$param_name;
	if(defined($param_host)) {
		$dsn.=';host='.$param_host;
	}
	if(defined($param_port)) {
		$dsn.=';=port'.$param_port;
	}
	my($dbh)=DBI->connect($dsn, $param_user, $param_pass, {
		RaiseError => 1,
		AutoCommit => 0,
		mysql_enable_utf8 => 1,
	}) or die 'Connection Error: '.$DBI::errstr;
	#$dbh->{HandleError} =\&handle_error;
	return $dbh;
}

sub get_fields($$) {
	my($dbh, $table)=@_;
	if($debug) {
		print 'table is '.$table."\n";
	}
	my($sql)='SELECT column_name FROM information_schema.columns WHERE table_name=? AND table_schema=DATABASE()';
	my($sth)=$dbh->prepare($sql);
	$sth->execute($table) or die 'SQL Error: '.$DBI::errstr;
	my($rowhashref);
	my(%hash);
	while($rowhashref=$sth->fetchrow_hashref()) {
		my($column_name)=$rowhashref->{'column_name'};
		$hash{$column_name}=defined;
	}
	if($debug) {
		print(Dumper(\%hash));
	}
	return \%hash;
}

my($param_rcfile)=File::HomeDir->my_home.'/.myworldrc';
my($param_table)=undef;
GetOptions(
	'rcfile=s' => \$param_rcfile,
	'table=s' => \$param_table,
) or die 'error in command line parsing';

if (!defined($param_table)) {
	die 'must set table using --table';
}

my($dbh)=db_connect($param_rcfile);
my($hash)=get_fields($dbh, $param_table);
$dbh->disconnect();
