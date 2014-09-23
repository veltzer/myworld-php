#!/usr/bin/python3

'''
this script will install all the required packages that you need on
ubuntu to compile and work with this package.
'''

import subprocess # for check_call

packs=[
	'yui-compressor', # for compressing javascript
	'python-pythonmagick', # for inserting blobs into the database
	'imagemagick', # for convert(1) for image manipulation
	'python-pip', # so that I could install python packages via pip
	'python3-pip', # so that I could install python packages via pip
	'python-id3', # mp3 tagging library
	'python-eyed3', # mp3 tagging library
	'python-mutagen', # mp3 tagging library
	'youtube-dl', # for youtube-dl
	'python-imdbpy', # for imdb python module
]

args=[
	'sudo',
	'apt-get',
	'install',
	'--assume-yes'
]
args.extend(packs)
subprocess.check_call(args)

if False:
	subprocess.check_call([
		'pip',
		'install',
		'--user',
		'--upgrade',
		'PyGithub',
	])
