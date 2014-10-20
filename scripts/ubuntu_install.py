#!/usr/bin/python3

'''
this script will install all the required packages that you need on
ubuntu to compile and work with this package.
'''

import subprocess # for check_call

packs=[
	# javascript
	'yui-compressor', # for compressing javascript

	# python
	'python-pythonmagick', # for inserting blobs into the database
	'imagemagick', # for convert(1) for image manipulation
	'python-pip', # so that I could install python packages via pip
	'python3-pip', # so that I could install python packages via pip
	'python-id3', # mp3 tagging library
	'python-eyed3', # mp3 tagging library
	'python-mutagen', # mp3 tagging library
	'youtube-dl', # for youtube-dl
	'python-imdbpy', # for imdb python module
	'python-enzyme', # for video meta data
	'python3-enzyme', # for video meta data
	'python-mediainfodll', # for video meta data
	'python3-mediainfodll', # for video meta data
	'python-kaa-metadata', # for video meta data
	'python-bs4', # for html parsing
	'python3-bs4', # for html parsing
	'python-mysqldb', # for imdb API

	# perl
	'libimdb-film-perl', # for perl access to imdb
	'libxml-simple-perl',
	'libyaml-perl',
	'libdate-manip-perl',
	'libmp3-info-perl',
	'libvideo-info-perl',
]

args=[
	'sudo',
	'apt-get',
	'install',
	'--assume-yes'
]
args.extend(packs)
subprocess.check_call(args)
