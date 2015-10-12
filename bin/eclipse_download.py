#!/usr/bin/python3

'''
This is a script that knows how to download eclipse.

These are examples of urls to download
http://kambing.ui.ac.id/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk-x86_64.tar.gz
http://mirrors.hustunique.com/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk.tar.gz

TODO:
- make this script convert the files to .xz storage.
'''

import download.generic # for get
import os.path # for isfile
import hashlib # for new

def hexdigest(filename, algo):
	BLOCKSIZE = 65536
	hasher = hashlib.new(algo)
	with open(filename, 'rb') as afile:
		buf = afile.read(BLOCKSIZE)
		while len(buf) > 0:
			hasher.update(buf)
			buf = afile.read(BLOCKSIZE)
	return hasher.hexdigest()

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
cs_type='sha512'

for product, incubation in products:
	if incubation:
		incubation_str='-incubation'
	else:
		incubation_str=''
	for platform in platforms:
		url='{mirror}/{release}/{version}/eclipse-{product}-{release}-{version}{incubation_str}-linux-gtk{platform}.tar.gz'.format(**vars())
		cs_url='{mirror}/{release}/{version}/eclipse-{product}-{release}-{version}{incubation_str}-linux-gtk{platform}.tar.gz.{cs_type}'.format(**vars())
		filename='eclipse-{product}-{release}-{version}-linux-gtk{platform}.tar.gz'.format(**vars())
		cs_filename='eclipse-{product}-{release}-{version}-linux-gtk{platform}.tar.gz.{cs_type}'.format(**vars())
		if os.path.isfile(filename):
			print('skipping [{0}] from [{1}]...'.format(filename, url))
		else:
			download.generic.get(url, filename)
		if os.path.isfile(cs_filename):
			print('skipping [{0}] from [{1}]...'.format(cs_filename, cs_url))
		else:
			download.generic.get(cs_url, cs_filename)
		# read the checksum and compare
		with open(cs_filename) as f:
			read_cs=f.readline().split()[0]
			calc_cs=hexdigest(filename, cs_type)
			if read_cs!=calc_cs:
				print('checksum for [{0}] is bad...'.format(filename))
				sys.exit(1)
			else:
				print('checksum for [{0}] is good...'.format(filename))
