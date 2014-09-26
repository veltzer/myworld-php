#!/usr/bin/python3

'''
This script downloads all youtube videos referenced from the database to my google drive.
'''

# libraries
import myworld.db # for connect, print_results, get_results
import os.path # for join
import subprocess # for check_call
import download.ted # for get

# parameters
# where should the files be downloaded to?
p_folder='/home/mark/download'
p_folder='/home/mark/slow_links/emovies/youtube'
p_folder='/mnt/external/mark/topics_archive/video/emovies'
# report progress?
p_progress=False
# report on downloads?
p_download_report=True

# code
conn=myworld.db.connect()

sql='''
SELECT TbWkWorkExternal.externalCode, TbWkWork.name, TbExternalType.template, TbExternalType.name as tname FROM TbWkWorkExternal, TbExternalType, TbWkWork WHERE
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbWkWorkExternal.workId=TbWkWork.id AND
	TbExternalType.name in ('youtube_video_id', 'ted_video_id')
'''

res=myworld.db.get_results(conn, sql)
stat_count=0
stat_download=0
for row in res:
	f_externalCode=row['externalCode']
	f_name=row['name']
	f_template=row['template']
	f_tname=row['tname']
	if p_progress:
		print('doing work [{0}] code [{1}]...'.format(f_name, f_externalCode))
	file=os.path.join(p_folder, f_tname, f_externalCode)
	stat_count+=1
	if os.path.isfile(file):
		if p_progress:
			print('file is already there...')
		continue
	url=f_template.replace('$external_id', f_externalCode)
	if p_download_report:
		print('downloading [{0}] from [{1}], [{2}]...'.format(file, url, f_name))
	if f_tname=='youtube_video_id':
		subprocess.call([
			'youtube-dl',
			url,
			'--output',
			file,
		])
	if f_tname=='ted_video_id':
		download.ted.get(url, file)
	stat_download+=1

conn.close()
print('stat_count [{0}]'.format(stat_count))
print('stat_download [{0}]'.format(stat_download))
