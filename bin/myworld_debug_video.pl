#!/usr/bin/perl -w

=head

This script debugs the Video::Info module.

=cut

use strict;
use diagnostics;
use Video::Info qw();

my($info)=Video::Info->new(-file=>$ARGV[0]);
print($info);
