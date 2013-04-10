#!/usr/bin/perl -w

# This script import languages into my database.
# The languages are imported from the file: /usr/share/xml/iso-codes/iso_639.xml
# which is part of the iso-codes package.
#
# TODO:
#

use strict;
use diagnostics;
use DBI;
use Error qw(:try);
use XML::DOM qw();

# parameters

# table to update
my($param_table)='TbLanguages';
# file to import from
my($file_name)='/usr/share/xml/iso-codes/iso_639.xml';
# should we actually work with the database ?
my($do_db)=1;
# should we debug the script ?
my($debug)=0;
# print stats at the end ?
my($stats)=1;

# here starts the script...
my($imported)=0;
my($dbh);

sub handle_error() {
	my($rc)=$dbh->err;
	my($str)=$dbh->errstr;
	my($rv)=$dbh->state;
	throw Error::Simple($str.','.$rv.','.$rv);
}

if($do_db) {
	$dbh=DBI->connect('dbi:mysql:myworld','','',{
		RaiseError => 1,
		AutoCommit => 0,
		mysql_enable_utf8 => 1,
	});
}

# Lets parse the file via DOM
my($parser)=new XML::DOM::Parser();
my($doc)=$parser->parsefile($file_name);
# do not put parens on next statement! (list context)
my $nodes=$doc->getElementsByTagName('iso_639_entry');
for (my($i)=0;$i<$nodes->getLength();$i++) {
	my($node)=$nodes->item($i);
	my($has_1)=0;
	my($f_iso_639_2B_code)=$node->getAttributeNode('iso_639_2B_code')->getValue();
	my($f_iso_639_2T_code)=$node->getAttributeNode('iso_639_2T_code')->getValue();
	# the _1_ code does not exist for all languages...
	my($f_iso_639_1_code);
	my($node_1)=$node->getAttributeNode('iso_639_1_code');
	if(defined($node_1)) {
		$f_iso_639_1_code=$node_1->getValue();
		$has_1=1;
	}
	my($f_name)=$node->getAttributeNode('name')->getValue();
	if($debug) {
		print 'f_iso_639_2B_code is '.$f_iso_639_2B_code."\n";
		print 'f_iso_639_2T_code is '.$f_iso_639_2T_code."\n";
		if($has_1) {
			print 'f_iso_639_1_code is '.$f_iso_639_1_code."\n";
		}
		print 'f_name is '.$f_name."\n";
	}
	if($do_db) {
		# now insert the record into the database
		if($has_1) {
			$dbh->do("INSERT INTO TbLanguage (iso_639_2B_code,iso_639_2T_code,iso_639_1_code,name) VALUES(?,?,?,?)",
				undef,
				$f_iso_639_2B_code,
				$f_iso_639_2T_code,
				$f_iso_639_1_code,
				$f_name,
			);
		} else {
			$dbh->do("INSERT INTO TbLanguage (iso_639_2B_code,iso_639_2T_code,name) VALUES(?,?,?)",
				undef,
				$f_iso_639_2B_code,
				$f_iso_639_2T_code,
				$f_name,
			);
		}
		$imported++;
	}
}

if($do_db) {
	# now commit all the changes...
	$dbh->commit();
	# disconnect from the database
	$dbh->disconnect();
}

if($stats) {
	print 'imported is ['.$imported.']'."\n";
}
