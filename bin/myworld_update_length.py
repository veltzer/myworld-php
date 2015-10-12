#!/usr/bin/python3

'''
update legths for youtube movies in myworld.
'''

#############
# libraries #
#############
import myworld.db # for connect, get_cursor
import MediaInfoDLL3 # for Stream, MediaInfo
import os.path # for join
import os # for stat
import stat # for ST_SIZE
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
p_query_types=set([
	'youtube_video_id',
	'ted_video_id',
	'download_url',
])

#############
# functions #
#############
def update_length(conn, curr, f_id, val):
	if p_do_progress:
		print('updating length to [{0}]'.format(val))
	if p_doit:
		curr.execute('UPDATE TbWkWork SET length=%s, updatedLengthDate=NOW() WHERE id=%s',(val, f_id))
		conn.commit()
def update_size(conn, curr, f_id, val):
	if p_do_progress:
		print('updating size to [{0}]'.format(val))
	if p_doit:
		curr.execute('UPDATE TbWkWork SET size=%s, updatedSizeDate=NOW() WHERE id=%s',(val, f_id))
		conn.commit()
def get_length(filename):
	MI=MediaInfoDLL3.MediaInfo()
	MI.Open(filename)
	duration_string = MI.Get(MediaInfoDLL3.Stream.Video, 0, 'Duration')
	MI.Close()
	duration = int(duration_string)//1000
	return duration
def get_size(filename):
	st=os.stat(filename)
	return st[stat.ST_SIZE]

########
# code #
########
conn=myworld.db.connect()
curr=myworld.db.get_cursor(conn)
curr2=myworld.db.get_cursor(conn)

sql='''
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
	TbExternalType.name IN ('{0}')
'''.format('\',\''.join(p_query_types))
stat_did=0
curr.execute(sql)
results=curr.fetchall()
for result in results:
	f_id=result['id']
	f_name=result['name']
	f_externalCode=result['externalCode']
	f_tname=result['tname']
	if p_do_progress:
		print('doing [{0}]...'.format(f_name))
	filename=myworld.utils.filename_switch(p_folder, f_tname, f_externalCode)
	if not os.path.isfile(filename):
		print('file [{0}] does not exist, download it first...'.format(filename))
		continue
	update_length(conn, curr2, f_id, get_length(filename))
	update_size(conn, curr2, f_id, get_size(filename))
	stat_did+=1

curr.close()
conn.close()

if p_do_stats:
	print('stat_did [{0}]'.format(stat_did))
