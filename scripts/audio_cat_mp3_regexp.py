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
for x in range(1,13):
	newx="%02d" % (x,)
	print(newx)
	l1=sorted(glob.glob("TTC - Utopia & Terror in the 20th Century CD %s* - 0[0-6] *" % (str(newx),)))
	l2=sorted(glob.glob("TTC - Utopia & Terror in the 20th Century CD %s* - 0[7-9] *" % (str(newx),)))
	l3=sorted(glob.glob("TTC - Utopia & Terror in the 20th Century CD %s* - 1[0-2] *" % (str(newx),)))
	l2.extend(l3)
	l2=sorted(l2)
	print(l1)
	print(l2)
	assert len(l1)==6
	assert len(l2)==6
	lect+=1
	res="lecture%02d.mp3" % (lect)
	unite(l1, res)
	lect+=1
	res="lecture%02d.mp3" % (lect)
	unite(l2, res)
