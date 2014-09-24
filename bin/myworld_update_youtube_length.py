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

##############
# parameters #
##############
# where should the files be downloaded to?
p_folder='/mnt/external/mark/topics_archive/video/emovies/youtube'
# do statistics?
p_do_stats=True
# do progress?
p_do_progress=True
# really update the database?
p_doit=True

#############
# functions #
#############
def get_filename(code):
	return os.path.join(p_folder, code)
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
	duration_string = MI.Get(MediaInfoDLL3.Stream.Video, 0, "Duration")
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
SELECT TbWkWork.id, TbWkWork.name, TbWkWorkExternal.externalCode FROM TbWkWork, TbWkWorkExternal, TbExternalType WHERE
	( TbWkWork.updatedLengthDate IS NULL OR
	TbWkWork.updatedSizeDate IS NULL ) AND
	TbWkWorkExternal.workId=TbWkWork.id AND
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbExternalType.name='youtube_video_id'
'''
stat_did=0
curr.execute(sql)
results=curr.fetchall()
for result in results:
	f_externalCode=result['externalCode']
	f_name=result['name']
	f_id=result['id']
	if p_do_progress:
		print('doing [{0}]...'.format(f_name))
	filename=get_filename(f_externalCode)
	update_length(conn, curr2, f_id, get_length(filename))
	update_size(conn, curr2, f_id, get_size(filename))
	stat_did+=1

curr.close()
conn.close()

if p_do_stats:
	print('stat_did [{0}]'.format(stat_did))
