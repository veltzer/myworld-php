#!/usr/bin/python

from __future__ import print_function
import glob # for glob
import os.path # for split, join, isfile
import subprocess # for check_call
import os # for chdir

for gitfolder in glob.glob('*/.git'):
	folder=os.path.split(gitfolder)[0]
	project=folder
	if not os.path.isfile(os.path.join(folder,'.skip')):
		print('doing [{project}]'.format(project=project))
		os.chdir(folder)
		subprocess.check_call([
			'git',
			'pull',
#			'--tags',
		])
		os.chdir('..')
	else:
		print('skipping [{project}]'.format(project=project))
