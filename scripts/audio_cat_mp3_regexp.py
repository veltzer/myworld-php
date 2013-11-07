#!/usr/bin/python3

"""
Script to be used to catenate many mp3 files.

	Mark Veltzer <mark@veltzer.net>
"""

import subprocess
import glob

doRun=True
doDebug=False

def unite(l, out):
	args=[ 'ffmpeg', '-i', 'concat:'+'|'.join(l), '-acodec', 'copy', out ]
	if doRun:
		subprocess.check_call(args)
	else:
		print(l,out)

'''
lect=0
for x in range(1,33):
	newx="%02d" % (x,)
	print(newx)
	l=sorted(glob.glob("%s-*" % (str(newx),)))
	if doDebug:
		print(l)
	assert len(l)==6
	lect+=1
	name=l[0][6:]
	res="%02d - %s" % (lect,name)
	print("new name is", res)
	unite(l, res)
'''
l=[]
count=1
lect=0
for f in sorted(glob.glob("*.mp3")):
	l.append(f)
	if count%6==0:
		name=f[4:]
		lect+=1
		res="%02d - %s" % (lect,name)
		unite(l, res)
		l=[]
		count=1
	else:
		count+=1
