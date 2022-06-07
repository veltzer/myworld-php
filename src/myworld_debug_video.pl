#!/usr/bin/perl -w

=head

This script debugs the Video::Info module.

=cut

use strict;
use diagnostics;
use Video::Info qw();
use Data::Dumper qw();

print("version is ".$Video::Info::VERSION."\n");
my($info)=Video::Info->new(-file=>$ARGV[0]);
print Data::Dumper::Dumper($info);
