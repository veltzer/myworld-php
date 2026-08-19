#!/usr/bin/python3

'''
update lengths for youtube movies in myworld.
'''

#############
# libraries #
#############
import os # for stat
import os.path # for join, isfile
import stat # for ST_SIZE

import MediaInfoDLL3 # for Stream, MediaInfo

import myworld.db # for connect, get_cursor
import myworld.utils # for filename_switch

##############
# parameters #
##############
# where should the files be downloaded to?
p_folder='/mnt/seagate/mark/topics_archive/video/emovies/download'
# do statistics?
p_do_stats=True
# do progress?
p_do_progress=True
# really update the database?
p_doit=True
# what types of urls to do the query on?
p_query_types={
    'youtube_video_id',
    'ted_video_id',
    'download_url',
}

#############
# functions #
#############
def update_length(conn, curr, f_id, val):
    """ Store the media length for a work. """
    if p_do_progress:
        print(f'updating length to [{val}]')
    if p_doit:
        curr.execute('UPDATE TbWkWork SET length=%s, updatedLengthDate=NOW() WHERE id=%s',
                     (val, f_id))
        conn.commit()

def update_size(conn, curr, f_id, val):
    """ Store the file size for a work. """
    if p_do_progress:
        print(f'updating size to [{val}]')
    if p_doit:
        curr.execute('UPDATE TbWkWork SET size=%s, updatedSizeDate=NOW() WHERE id=%s',
                     (val, f_id))
        conn.commit()

def get_length(filename):
    """ Media duration in seconds via libmediainfo. """
    media_info=MediaInfoDLL3.MediaInfo()
    media_info.Open(filename)
    duration_string=media_info.Get(MediaInfoDLL3.Stream.Video, 0, 'Duration')
    media_info.Close()
    return int(duration_string)//1000

def get_size(filename):
    """ File size in bytes. """
    return os.stat(filename)[stat.ST_SIZE]

def main():
    """ main entry point """
    conn=myworld.db.connect()
    curr=myworld.db.get_cursor(conn)
    curr2=myworld.db.get_cursor(conn)

    types_in = "','".join(p_query_types)
    sql=f'''
    SELECT
        TbWkWork.id, TbWkWork.name, TbWkWorkExternal.externalCode, TbExternalType.name AS tname
    FROM
        TbWkWork, TbWkWorkExternal, TbExternalType, TbWkWorkType
    WHERE
        ( TbWkWork.updatedLengthDate IS NULL OR TbWkWork.updatedSizeDate IS NULL ) AND
        TbWkWorkExternal.workId=TbWkWork.id AND
        TbWkWorkExternal.externalId=TbExternalType.id AND
        TbWkWork.typeId=TbWkWorkType.id AND
        TbWkWorkType.isVideo AND
        TbExternalType.name IN ('{types_in}')
    '''
    stat_did=0
    curr.execute(sql)
    for result in curr.fetchall():
        f_id=result['id']
        f_name=result['name']
        f_externalCode=result['externalCode']
        f_tname=result['tname']
        if p_do_progress:
            print(f'doing [{f_name}]...')
        filename=myworld.utils.filename_switch(p_folder, f_tname, f_externalCode)
        if not os.path.isfile(filename):
            print(f'file [{filename}] does not exist, download it first...')
            continue
        update_length(conn, curr2, f_id, get_length(filename))
        update_size(conn, curr2, f_id, get_size(filename))
        stat_did+=1

    curr.close()
    conn.close()

    if p_do_stats:
        print(f'stat_did [{stat_did}]')

if __name__=='__main__':
    main()
