#!/usr/bin/python

"""
This script gives track number according to order.
use like this: [script name] *.mp3
"""

from __future__ import print_function
import subprocess # for check_call
import optparse # for OptionParser
import os.path # for isfile
import os # for stat
import sys # for argv, exit
import stat # for ST_IWRITE
import ID3 # for ID3

# first check that all files are there
for file in sys.argv[1:]:
	if not os.path.isfile(file):
		print('file {file} is not there!'.format(file=file))
		sys.exit(1)
	st=os.stat(file)
	mode=st[stat.ST_MODE]
	if not mode & stat.S_IWRITE:
		print('file {file} is not writable!'.format(file=file))
		sys.exit(1)
# now change the track numbers
for i, file in enumerate(sys.argv[1:]):
	id3info = ID3.ID3(file)
	#print(id3info)
	id3info['TRACKNUMBER'] = i+1
	#id3info['TITLE'] = str(i+1)
	#print(id3info)
	id3info.write()
