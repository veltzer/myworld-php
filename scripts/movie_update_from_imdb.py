#!/usr/bin/python2

'''
first phase: read all lengths of all movies from imdb
'''

from __future__ import print_function
import MySQLdb # for connect
import imdb # for IMDb
import sys # for stdout, getdefaultencoding

params={
	'db': 'myworld',
	'read_default_file': '~/.my.cnf',
}
connection=imdb.IMDb()
out_encoding=sys.stdout.encoding or sys.getdefaultencoding()
with MySQLdb.connect(**params) as cursor:
	sql='SELECT externalCode,workId FROM TbWkWorkExternal'
	cursor.execute(sql)
	ids={}
	for x in cursor:
		f_externalCode=x[0]
		f_workId=x[1]
		ids[f_workId]=f_externalCode

	sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\')'
	cursor.execute(sql)
	for x in cursor:
		f_id=x[0]
		f_name=x[1]
		f_length=x[2]
		f_external=ids[f_id]
		movie=connection.get_movie(f_external)
		#info_runtime=int(movie.get('runtime')[0].encode(out_encoding, 'replace'))*60
		info_runtime=movie.get('runtime')
		#print('f_name: {0}'.format(f_name))
		#print('f_length: {0}'.format(f_length))
		#print('info_runtime: {0}'.format(info_runtime))
		print('{0}'.format(info_runtime))
