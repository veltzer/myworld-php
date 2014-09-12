#!/usr/bin/python3

'''
This script downloads all youtube videos referenced from the database to my google drive.
'''

# libraries
import myworld.db # for connect, print_results, get_results
import os.path # for join

# parameters
# where should the files be downloaded to?
p_folder='/home/mark/download'
# report progress?
p_progress=True

# code
conn=myworld.db.connect()

#myworld.db.print_results(conn, 'SELECT VERSION()')

sql='''
SELECT TbWkWorkExternal.externalCode FROM TbWkWorkExternal, TbExternalType WHERE
	TbWkWorkExternal.externalId=TbExternalType.id AND
	TbExternalType.name='youtube_video_id'
'''
res=myworld.db.get_results(conn, sql)
for row in res:
	f_externalCode=row['externalCode']
	file=os.path.join(p_folder, f_externalCode)
	if os.path.isfile(file):
		next
	if p_progress:
		print('downloading [{0}]...'.format(file))

conn.close()
