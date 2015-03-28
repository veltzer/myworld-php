#!/usr/bin/perl -w
use strict;
use warnings;

# http://www.andrewault.net/2010/07/09/perl-script-to-get-video-details/
 
use IPC::Open3;
 
# example
my $filename = $ARGV[0];
my %videoInfo = videoInfo($filename);
print "duration: " . $videoInfo{'duration'} . "\n";
print "durationsecs: " . $videoInfo{'durationsecs'} . "\n";
print "bitrate: " . $videoInfo{'bitrate'} . "\n";
print "vcodec: " . $videoInfo{'vcodec'} . "\n";
print "vformat: " . $videoInfo{'vformat'} . "\n";
print "acodec: " . $videoInfo{'acodec'} . "\n";
print "asamplerate: " . $videoInfo{'asamplerate'} . "\n";
print "achannels: " . $videoInfo{'achannels'} . "\n";
 
#
# returns media information in a hash
sub videoInfo {
	# ffmpeg command
	my $ffmpeg = '/usr/local/bin/ffmpeg';
 
	my %finfo = (
				'duration' => "00:00:00.00",
				'durationsecs' => "0",
				'bitrate' => "0",
				'vcodec' => "",
				'vformat' => "",
				'acodec' => "",
				'asamplerate' => "0",
				'achannels' => "0", 
	);
 
	my $file = shift;
 
	# escaping characters
	$file =~ s/(\W)/\\$1/g;
 
	open3( "</dev/null", ">/dev/null", \*ERPH, "$ffmpeg -i $file" ) or die "can't run $ffmpeg\n";
	my @res = <ERPH>;
 
	# parse ffmpeg output
	foreach (@res) {
	print;
 
		# duration
		if (m!Duration: ([0-9][0-9]:[0-9][0-9]:[0-9][0-9].[0-9][0-9])!) {
			$finfo{'duration'} = $1;
		}
 
		# bitrate
		if (m!bitrate: (\d*) kb/s!) {
			$finfo{'bitrate'} = $1;
		}
 
		# vcodec and vformat
		if (/Video: (\w*), (\w*),/) {
			$finfo{'vcodec'} = $1;
			$finfo{'vformat'} = $2;
		}
 
	# Stream #0.1(und): Audio: aac, 48000 Hz, 1 channels, s16, 64 kb/s
 
		# acodec, samplerate, stereo and audiorate
		if (m!Audio: (\w*), (\d*) Hz, (\d*)!) {
			$finfo{'acodec'} = $1;
			$finfo{'asamplerate'} = $2;
			$finfo{'achannels'} = $3;
		}
	}
 
	my $tenths = substr( $finfo{'duration'}, 9, 2 );
	my $seconds = substr( $finfo{'duration'}, 6, 2 );
	my $minutes = substr( $finfo{'duration'}, 3, 2 );
	my $hours = substr( $finfo{'duration'}, 0, 2 );
	$finfo{'durationsecs'} = ( $tenths * .01 ) + $seconds + ( $minutes * 60 ) + ( $hours * 360 );
 
	return %finfo;
}

