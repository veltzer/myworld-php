#!/usr/bin/python

"""
This script will catenate mp3 files correctly using ffmpeg.
see: http://superuser.com/questions/314239/how-to-join-merge-many-mp3-files
"""

import subprocess
import sys

if len(sys.argv)<3:
	raise ValueError('usage: outfile.mp3 [infile1.mp3] [infile2.mp3] ...')

args=['ffmpeg','-i','concat:'+'|'.join(sys.argv[2:]),'-acodec','copy',sys.argv[1]]
#print args
subprocess.check_call(args)
