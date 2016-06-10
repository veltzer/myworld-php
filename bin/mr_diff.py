#!/usr/bin/python3

'''
This script prints repos which are not registered in mr.

It will first read the projects registered in ~/.mrconfig
'''

import os.path # for expanduser, isdir, join

have_mrconfig=set()
filename=os.path.expanduser('~/.mrconfig')
for line in open(filename):
	line=line.rstrip()
	if line.startswith('['):
		project=line[1:-1].split('/')[-1]
		have_mrconfig.add(project)

have_folder=set()
base=os.path.expanduser('~/git')
for f in os.listdir(base):
	full=os.path.join(base, f)
	if os.path.isdir(full):
		have_folder.add(f)

if have_folder!=have_mrconfig:
	print(have_folder ^ have_mrconfig)
