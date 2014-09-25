#!/usr/bin/python3

'''
This script checks the sanity of the myworld database.

# here is what it should check:
# 1. AUTO_INCREMENT always implies tight packing of the table.
# 2. no works without views.
# 3. no works without reviews.
# 4. no works without lengths.
# 5. no person without single group representing him.
# 6. no works without people involved (at least one).
# 7. no person without external id of some sort (at least one).
# more to come...

Currently it does very little.
'''

#############
# libraries #
#############
import myworld.db # for connect, get_results

##############
# parameters #
##############

########
# code #
########
conn=myworld.db.connect()

sql='''
SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork, TbWkWorkType WHERE
	length IS NULL AND
	updatedLengthDate IS NULL
	AND TbWkWork.typeId=TbWkWorkType.id
	AND TbWkWorkType.isVideo=TRUE
'''
results=myworld.db.get_results(conn, sql)
if len(results)>0:
	print('got works with no length')
	for result in results:
		print('\t{0}'.format(result['name']))

print('checking bad work names')
sql='''
SELECT TbWkWork.id,TbWkWork.name FROM TbWkWork
'''
results=myworld.db.get_results(conn, sql)
for result in results:
	f_id=result['id']
	f_name=result['name']
	if f_name.strip()!=f_name:
		print('got bad name of work for id [{0}] and name [{1}]'.format(f_id, f_name))

conn.close()
