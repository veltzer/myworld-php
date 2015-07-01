#!/usr/bin/python3

'''
This is a script that knows how to download eclipse.
'''

import download.generic # for get

'''
These are examples of urls to download
http://kambing.ui.ac.id/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk-x86_64.tar.gz
http://mirrors.hustunique.com/eclipse/technology/epp/downloads/release/luna/SR2/eclipse-cpp-luna-SR2-linux-gtk.tar.gz
'''

protocol='http'
mirror='mirror.hust.edu.cn'
mirror='mirrors.hustunique.com'
mirror='kambing.ui.ac.id'
mirror='ftp.jaist.ac.jp'
mirror='http://ftp.jaist.ac.jp/pub/eclipse/technology/epp/downloads/release'
products=[
#	('automotive', True),
#	('cpp', False),
#	('dsl', False),
#	('java', False),
#	('jee', False),
#	('modeling', False),
#	('parallel', False),
#	('php', False),
#	('rcp', False),
#	('reporting', False),
#	('scout', False),
#	('standard', False),
#	('testing', False),
	('committers', False),
]
#version='SR2'
version='R'
#release='luna'
release='mars'
platforms=[
	'-x86_64',
	'',
]

for product, incubation in products:
	if incubation:
		incubation_str='-incubation'
	else:
		incubation_str=''
	for platform in platforms:
		#url='{protocol}://{mirror}/eclipse/technology/epp/downloads/release/{release}/{version}/eclipse-{product}-{release}-{version}{incubation_str}-linux-gtk{platform}.tar.gz'.format(**vars())
		url='{mirror}/{release}/{version}/eclipse-{product}-{release}-{version}{incubation_str}-linux-gtk{platform}.tar.gz'.format(**vars())
		filename='eclipse-{product}-{release}-{version}-linux-gtk{platform}.tar.gz'.format(**vars())
		download.generic.get(url, filename)
