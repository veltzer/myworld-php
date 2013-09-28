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
		'''
		subprocess.check_call([
			'git',
			'diff',
			'--name-only',
		])
		'''
		subprocess.check_call([
			'git',
			'status',
			'--short',
		])
		os.chdir('..')
		'''
		old code in bash was...
		git diff --name-only
		git status | grep "Changed but not updated"
		git status | grep "Your branch is ahead"
		git status | grep "Untracked files"
		'''
	else:
		print('skipping [{project}]'.format(project=project))
