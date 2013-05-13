#!/usr/bin/python

"""
This application imports images into the database

Example of usage:
	./dbdata_import_image.py ~/downloads/earlysense.jpg EarlySense earlysense
"""

from __future__ import print_function
import subprocess
import sys
import os
import MySQLdb

if len(sys.argv)!=4:
	raise ValueError('usage: [image] [name] [slug]')

filename=sys.argv[1]
name=sys.argv[2]
slug=sys.argv[3]

filename_small='/tmp/small.jpg'
filename_large='/tmp/large.jpg'
args=['file','--brief','--mime-type',filename]
mime=subprocess.check_output(args)
mime=mime.decode('utf-8')
mime=mime.rstrip()
#print('mime is ['+mime+']')
try:
	os.unlink(filename_small)
	os.unlink(filename_large)
except:
	pass
size_large='256x128'
size_small='64x32'
args=[
	'convert','-background','white','-type','TrueColorMatte','-gravity','center',
	'-resize',size_small,'-extent',size_small,filename,filename_small,
]
subprocess.check_call(args)
args=[
	'convert','-background','white','-type','TrueColorMatte','-gravity','center',
	'-resize',size_large,'-extent',size_large,filename,filename_large,
]
subprocess.check_call(args)
db=MySQLdb.connect(
	host='localhost', # your host, usually localhost
	user='mark', # your username
	passwd='', # your password
	db='myworld'
)
data=open(filename,'rb').read()
data_small=open(filename_small,'rb').read()
data_large=open(filename_large,'rb').read()
cur=db.cursor()
cur.execute('INSERT INTO TbImage (name,slug,smallMime,largeMime,origMime,smallData,largeData,origData) '+
	'VALUES(%s,%s,%s,%s,%s,%s,%s,%s)',(name,slug,'image/jpeg','image/jpeg',mime,data_small,data_large,data))
print('db.insert_id() is',db.insert_id())
cur.close()
db.commit()
db.close()
