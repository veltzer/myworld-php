#!/usr/bin/python3

'''
This script downloads all youtube videos referenced from the database to my
google drive.
'''

###########
# imports #
###########
import os  # for rename
import os.path  # for join, isfile
import subprocess  # for check_call

import download.generic  # for get
import download.ted  # for get
import myworld.db  # for connect, print_results, get_results
import myworld.utils  # for filename_switch

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
p_do_types={
    'youtube_video_id',
    'ted_video_id',
    'download_url',
}
# what types of urls to do the query on?
p_query_types={
    'youtube_video_id',
    'ted_video_id',
    'download_url',
}
p_print_stats=True

#############
# functions #
#############
def download_switch(f_tname, url, file):
    """ Dispatch a download to the right backend for its external type. """
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

def main():
    """ main entry point """
    conn=myworld.db.connect()

    all_types=set()
    sql='''
    SELECT
        TbExternalType.name
    FROM
        TbExternalType
    '''
    for row in myworld.db.get_results(conn, sql):
        all_types.add(row['name'])

    types_in = "','".join(p_query_types)
    sql=f'''
    SELECT
        TbWkWorkExternal.externalCode, TbWkWork.name, TbExternalType.template, TbExternalType.name AS tname
    FROM
        TbWkWorkExternal, TbExternalType, TbWkWork, TbWkWorkType
    WHERE
        TbWkWorkExternal.externalId=TbExternalType.id AND
        TbWkWorkExternal.workId=TbWkWork.id AND
        TbWkWork.typeId=TbWkWorkType.id AND
        TbWkWorkType.isVideo AND
        TbExternalType.name IN ('{types_in}')
    '''

    res=myworld.db.get_results(conn, sql)
    stat_count=0
    stat_already_there=0
    stat_download=0
    stat_download_by_type={t: 0 for t in all_types}
    stat_skipped_by_type={t: 0 for t in all_types}
    for row in res:
        f_externalCode=row['externalCode']
        f_name=row['name']
        f_template=row['template']
        f_tname=row['tname']
        if p_progress:
            print(f'doing work [{f_name}] code [{f_externalCode}] type [{f_tname}]...')
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
                print(f'downloading [{file}] from [{url}], [{f_name}]...')
            download_switch(f_tname, url, file)
            stat_download_by_type[f_tname]+=1
            stat_download+=1
        else:
            if p_report:
                print(f'skipping [{file}] from [{url}], [{f_name}]...')
            stat_skipped_by_type[f_tname]+=1

    conn.close()
    if p_print_stats:
        downloaded={x: y for (x, y) in stat_download_by_type.items() if y>0}
        skipped={x: y for (x, y) in stat_skipped_by_type.items() if y>0}
        print(f'stat_count [{stat_count}]')
        print(f'stat_already_there [{stat_already_there}]')
        print(f'stat_download [{stat_download}]')
        print(f'stat_download_by_type [{downloaded}]')
        print(f'stat_skipped_by_type [{skipped}]')

if __name__=='__main__':
    main()
