#!/usr/bin/python3

'''
This is a script that knows how to download eclipse.

These are examples of urls to download
http://kambing.ui.ac.id/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk-x86_64.tar.gz
http://mirrors.hustunique.com/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk.tar.gz

TODO:
- make this script check the sha1 checksums as well as downloading the packages.
'''

import download.generic # for get
import os.path # for isfile

protocol='http'
mirror='http://ftp.jaist.ac.jp/pub/eclipse/technology/epp/downloads/release'
products=[
	('automotive', True),
	('cpp', False),
	('dsl', False),
	('java', False),
	('jee', False),
	('modeling', False),
	('parallel', False),
	('php', False),
	('rcp', False),
	('reporting', False),
	('scout', False),
#	('standard', False),
	('testing', False),
	('committers', False),
]
version='1'
release='mars'
platforms=[
	'-x86_64', # x64
	'', # i386
]

for product, incubation in products:
	if incubation:
		incubation_str='-incubation'
	else:
		incubation_str=''
	for platform in platforms:
		url='{mirror}/{release}/{version}/eclipse-{product}-{release}-{version}{incubation_str}-linux-gtk{platform}.tar.gz'.format(**vars())
		filename='eclipse-{product}-{release}-{version}-linux-gtk{platform}.tar.gz'.format(**vars())
		if os.path.isfile(filename):
			print('skipping [{0}]...'.format(filename))
		else:
			download.generic.get(url, filename)
