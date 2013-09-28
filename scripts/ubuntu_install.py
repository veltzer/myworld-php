#!/usr/bin/python

'''
this scrip will install all the required packages that you need on
ubuntu to compile and work with this package.
'''

from __future__ import print_function
import subprocess # for check_call

packs=[
	'yui-compressor', # for compressing javascript
	'python-pythonmagick', # for inserting blobs into the database
	'imagemagick', # for convert(1) for image manipulation
]

args=['sudo','apt-get','install']
args.extend(packs)
subprocess.check_call(args)

subprocess.check_call([
	'pip',
	'install',
	'PyGithub',
])
