#!/usr/bin/python3

'''
This script downloads all youtube videos referenced from the database to my google drive.
'''

# libraries
import myworld.db # for connect, print_results, get_results
import os.path # for join
import subprocess # for check_call

# parameters
# where should the files be downloaded to?
p_folder='/home/mark/download'
p_folder='/home/mark/slow_links/emovies/youtube'
p_folder='/mnt/external/mark/topics_archive/video/emovies/youtube'
# report progress?
p_progress=False
# report on downloads?
p_download_report=True

# code
conn=myworld.db.connect()

#myworld.db.print_results(conn, 'SELECT VERSION()')

sql='''
SELECT template FROM TbExternalType WHERE name='youtube_video_id'
'''
res=myworld.db.get_results(conn, sql)
template=res[0]['template']
#print('template is [{0}]'.format(template))

sql='''
SELECT TbWkWorkExternal.externalCode, TbWkWork.name FROM TbWkWorkExternal, TbExternalType, TbWkWork WHERE
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbExternalType.name='youtube_video_id' AND
	TbWkWorkExternal.workId=TbWkWork.id
'''

res=myworld.db.get_results(conn, sql)
stat_count=0
stat_download=0
for row in res:
	f_externalCode=row['externalCode']
	f_name=row['name']
	if p_progress:
		print('doing work [{0}] code [{1}]...'.format(f_name, f_externalCode))
	file=os.path.join(p_folder, f_externalCode)
	stat_count+=1
	if os.path.isfile(file):
		if p_progress:
			print('file is already there...')
		continue
	url=template.replace('$external_id', f_externalCode)
	if p_download_report:
		print('downloading [{0}] from [{1}], [{2}]...'.format(file, url, f_name))
	subprocess.check_call([
		'youtube-dl',
		url,
		'--output',
		file,
	])
	stat_download+=1

conn.close()
print('stat_count [{0}]'.format(stat_count))
print('stat_download [{0}]'.format(stat_download))
