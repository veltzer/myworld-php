#!/usr/bin/python3

'''
This script re-encodes mp3 files. The idea is that this re-encoding
fixes lots of common problems with mp3 files like files which have bad
length because they were catenated badly.
'''

import subprocess # for check_call, call, DEVNULL
import sys # for argv
import tempfile # for NamedTemporaryFile
import shutil # for move
import os # for unlink

doRun=True
doDebug=False
doCheck=True
# do you want to redirect standard output?
doRedirect=False

def fix(l):
	print('fixing [{0}]...'.format(l))
	f=tempfile.NamedTemporaryFile(suffix='.mp3')
	out=f.name
	f.close()
	args=[
		'avconv',
		'-i',
		l,
		'-acodec',
		'copy',
		out,
		'-loglevel',
		'quiet'
	]
	if doRun:
		if doCheck:
			if doRedirect:
				subprocess.check_call(args, stdout=subprocess.DEVNULL)
			else:
				subprocess.check_call(args)
		else:
			if doRedirect:
				subprocess.call(args, stdout=subprocess.DEVNULL)
			else:
				subprocess.call(args)
	else:
		print(args)
	os.unlink(l)
	shutil.move(out, l)

for x in sys.argv[1:]:
	fix(x)
