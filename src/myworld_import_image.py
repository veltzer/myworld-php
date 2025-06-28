#!/usr/bin/python3

'''
This application imports one image into the database
'''

import subprocess # for check_output, check_call
import sys # for exit
import os # for unlink
import myworld.db # for connect

# parameters
size_large='256x128'
size_small='64x32'
filename_small='/tmp/small.jpg'
filename_large='/tmp/large.jpg'
target_mime='image/jpeg'

if len(sys.argv)!=4:
    print('usage: dbdata_import_image.py [image] [name] [slug]')
    print('example: dbdata_import_image.py earlysense.jpg EarlySense earlysense')
    sys.exit(1)

# command line parameters
filename=sys.argv[1]
name=sys.argv[2]
slug=sys.argv[3]

# find the mime type of the file
args=['file','--brief','--mime-type',filename]
mime=subprocess.check_output(args)
mime=mime.decode('utf-8')
mime=mime.rstrip()

# remove the tmp files if they exist
if os.path.isfile(filename_small):
    os.unlink(filename_small)
if os.path.isfile(filename_large):
    os.unlink(filename_large)

# create large and small images
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

# get the data for the files
data=open(filename,'rb').read()
data_small=open(filename_small,'rb').read()
data_large=open(filename_large,'rb').read()

# connect to the database and insert
conn=myworld.db.connect()
cur=conn.cursor()
cur.execute('INSERT INTO TbImage (name,slug,smallMime,largeMime,origMime,smallData,largeData,origData) '+
    'VALUES(%s,%s,%s,%s,%s,%s,%s,%s)',(name,slug,target_mime,target_mime,mime,data_small,data_large,data))
print(f'cur.lastrowid is [{cur.lastrowid}]')
cur.close()
conn.commit()
conn.close()
