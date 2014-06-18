#!/usr/bin/python3

"""
This script will catenate mp3 files correctly using ffmpeg.
see: http://superuser.com/questions/314239/how-to-join-merge-many-mp3-files

TODO:
- use command line parsing and have the output file be specified with -o.
This is because the current usage may cause the user to accidentaly
step on his own files.
"""

import subprocess
import sys

if len(sys.argv)<3:
	raise ValueError('usage: outfile.mp3 [infile1.mp3] [infile2.mp3] ...')

#args=[ 'ffmpeg', '-i', 'concat:'+'|'.join(sys.argv[2:]), '-acodec', 'copy', sys.argv[1]]
args=[ 'avconv', '-i', 'concat:'+'|'.join(sys.argv[2:]), '-acodec', 'copy', sys.argv[1], '-loglevel', 'quiet' ]
subprocess.check_call(args)
