#!/usr/bin/python3

'''
This application imports one image into the database
'''

import os  # for unlink
import subprocess  # for check_output, check_call
import sys  # for exit

import myworld.db  # for connect

# parameters
SIZE_LARGE='256x128'
SIZE_SMALL='64x32'
FILENAME_SMALL='/tmp/small.jpg'
FILENAME_LARGE='/tmp/large.jpg'
TARGET_MIME='image/jpeg'

def main():
    """ main entry point """
    if len(sys.argv)!=4:
        print('usage: dbdata_import_image.py [image] [name] [slug]')
        print('example: dbdata_import_image.py earlysense.jpg EarlySense earlysense')
        sys.exit(1)

    filename=sys.argv[1]
    name=sys.argv[2]
    slug=sys.argv[3]

    # find the mime type of the file
    mime=subprocess.check_output(
        ['file', '--brief', '--mime-type', filename],
    ).decode('utf-8').rstrip()

    # remove the tmp files if they exist
    for tmp in (FILENAME_SMALL, FILENAME_LARGE):
        if os.path.isfile(tmp):
            os.unlink(tmp)

    # create large and small images
    for size, target in ((SIZE_SMALL, FILENAME_SMALL), (SIZE_LARGE, FILENAME_LARGE)):
        subprocess.check_call([
            'convert', '-background', 'white', '-type', 'TrueColorMatte', '-gravity', 'center',
            '-resize', size, '-extent', size, filename, target,
        ])

    # get the data for the files
    with open(filename, 'rb') as stream:
        data=stream.read()
    with open(FILENAME_SMALL, 'rb') as stream:
        data_small=stream.read()
    with open(FILENAME_LARGE, 'rb') as stream:
        data_large=stream.read()

    # connect to the database and insert
    conn=myworld.db.connect()
    cur=conn.cursor()
    cur.execute(
        'INSERT INTO TbImage (name,slug,smallMime,largeMime,origMime,smallData,largeData,origData) '
        'VALUES(%s,%s,%s,%s,%s,%s,%s,%s)',
        (name, slug, TARGET_MIME, TARGET_MIME, mime, data_small, data_large, data))
    print(f'cur.lastrowid is [{cur.lastrowid}]')
    cur.close()
    conn.commit()
    conn.close()

if __name__=='__main__':
    main()
