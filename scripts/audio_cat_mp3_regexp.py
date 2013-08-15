#!/usr/bin/python3

"""
Script to be used to catenate many mp3 files.

	Mark Veltzer <mark@veltzer.net>
"""

import subprocess
import glob

def unite(l, out):
	args=[ 'ffmpeg', '-i', 'concat:'+'|'.join(l), '-acodec', 'copy', out ]
	subprocess.check_call(args)

lect=0
for x in range(1,25):
	newx="%02d" % (x,)
	print(newx)
	l=sorted(glob.glob("%s-*" % (str(newx),)))
	print(l)
	assert len(l)==6
	lect+=1
	name=l[0][7:]
	res="%02d - %s" % (lect,name)
	print("new name is", res)
	unite(l, res)
