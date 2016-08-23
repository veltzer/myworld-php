#!/usr/bin/python3

'''
This script downloads all youtube videos referenced from the database to my google drive.
'''

###########
# imports #
###########
import myworld.db # for connect, print_results, get_results
import os.path # for join
import os # for rename
import subprocess # for check_call
import download.ted # for get
import download.generic # for get
import urllib.parse # for urlparse
import myworld.utils # for filename_switch

#############
# functions #
#############
def download_switch(f_tname, url, file):
    if f_tname=='youtube_video_id':
        subprocess.call([
            'youtube-dl',
            url,
            '--output',
            file,
        ])
        for suff in ['mp4', 'mkv']:
            filename=file+'.'+suff
            if os.path.isfile(filename):
                os.rename(filename, file)
                break
    if f_tname=='ted_video_id':
        download.ted.get(url, file)
    if f_tname=='download_url':
        download.generic.get(url, file)

##############
# parameters #
##############
# where should the files be downloaded to?
p_folder='/mnt/seagate/mark/topics_archive/video/emovies/download'
# report progress?
p_progress=False
# report on downloads and skips?
p_report=True
# enable various kinds of downloads
p_do_types=set([
    'youtube_video_id',
    'ted_video_id',
    'download_url',
])
# what types of urls to do the query on?
p_query_types=set([
    'youtube_video_id',
    'ted_video_id',
    'download_url',
])
p_print_stats=True

########
# code #
########
conn=myworld.db.connect()

all_types=set()
sql='''
SELECT
    TbExternalType.name
FROM
    TbExternalType
'''
res=myworld.db.get_results(conn, sql)
for row in res:
    f_name=row['name']
    all_types.add(f_name)

sql='''
SELECT
    TbWkWorkExternal.externalCode, TbWkWork.name, TbExternalType.template, TbExternalType.name AS tname
FROM
    TbWkWorkExternal, TbExternalType, TbWkWork, TbWkWorkType
WHERE
    TbWkWorkExternal.externalId=TbExternalType.id AND
    TbWkWorkExternal.workId=TbWkWork.id AND
    TbWkWork.typeId=TbWkWorkType.id AND
    TbWkWorkType.isVideo AND
    TbExternalType.name IN ('{0}')
'''.format('\',\''.join(p_query_types))

res=myworld.db.get_results(conn, sql)
stat_count=0
stat_already_there=0
stat_download=0
stat_download_by_type={}
stat_skipped_by_type={}
for t in all_types:
    stat_download_by_type[t]=0
    stat_skipped_by_type[t]=0
for row in res:
    f_externalCode=row['externalCode']
    f_name=row['name']
    f_template=row['template']
    f_tname=row['tname']
    if p_progress:
        print('doing work [{0}] code [{1}] type [{2}]...'.format(f_name, f_externalCode, f_tname))
    file=myworld.utils.filename_switch(p_folder, f_tname, f_externalCode)
    stat_count+=1
    if os.path.isfile(file):
        if p_progress:
            print('file is already there...')
        stat_already_there+=1
        continue
    url=f_template.replace('$external_id', f_externalCode)
    if f_tname in p_do_types:
        if p_report:
            print('downloading [{0}] from [{1}], [{2}]...'.format(file, url, f_name))
        download_switch(f_tname, url, file)
        stat_download_by_type[f_tname]+=1
        stat_download+=1
    else:
        if p_report:
            print('skipping [{0}] from [{1}], [{2}]...'.format(file, url, f_name))
        stat_skipped_by_type[f_tname]+=1

conn.close()
if p_print_stats:
    print('stat_count [{0}]'.format(stat_count))
    print('stat_already_there [{0}]'.format(stat_already_there))
    print('stat_download [{0}]'.format(stat_download))
    print('stat_download_by_type [{0}]'.format(dict((x,y) for (x,y) in stat_download_by_type.items() if y>0)))
    print('stat_skipped_by_type [{0}]'.format(dict((x,y) for (x,y) in stat_skipped_by_type.items() if y>0)))
