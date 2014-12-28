#!/usr/bin/python3

'''
This script prints repos which are not registered in mr.

It will first read the projects registered in ~/.mrconfig
'''

import os.path # for expanduser

have_mrconfig=set()
filename=os.path.expanduser('~/.mrconfig')
for line in open(filename):
	line=line.rstrip()
	if line.startswith('['):
		project=line[1:-1].split('/')[-1]
		have_mrconfig.add(project)

have_folder=set()
for f in os.listdir('.'):
	if os.path.isdir(f):
		have_folder.add(f)

if have_folder!=have_mrconfig:
	print(have_folder ^ have_mrconfig)
