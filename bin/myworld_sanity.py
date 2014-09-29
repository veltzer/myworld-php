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
# check the reverse: that every file in the download folders corresponds to a work and external entity in my database.
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

print('checking for work views that have remarks or todos')
sql='''SELECT TbWkWorkView.workId FROM TbWkWorkView WHERE remark IS NOT NULL OR todo IS NOT NULL'''
results=myworld.db.get_results(conn, sql)
for result in results:
	print('\t{0}'.format(result))

print('checking works which are videos without length updated')
sql='''
SELECT
	TbWkWork.id,TbWkWork.name
FROM
	TbWkWork, TbWkWorkType
WHERE
	updatedLengthDate IS NULL
	AND TbWkWork.typeId=TbWkWorkType.id
	AND TbWkWorkType.isVideo=TRUE
'''
results=myworld.db.get_results(conn, sql)
for result in results:
	print('\t{0}'.format(result))

print('checking works with space in the begining or end of name')
sql='''
SELECT
	TbWkWork.id,TbWkWork.name
FROM
	TbWkWork
WHERE
	name REGEXP '^[[:space:]].*$' OR
	name REGEXP '^.*[[:space:]]$'
'''
results=myworld.db.get_results(conn, sql)
for result in results:
	print('\t{0}'.format(result))

print('checking for people that do not have external ids and are not friends')
sql='''
SELECT
	TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.surname
FROM
	TbIdPerson
WHERE
	NOT EXISTS (
		SELECT TbIdPersonExternal.id
		FROM TbIdPersonExternal
		WHERE TbIdPersonExternal.personId=TbIdPerson.id
	) AND NOT EXISTS (
		SELECT TbIdGrpPerson.id
		FROM TbIdGrpPerson, TbIdGrp
		WHERE TbIdGrpPerson.personId=TbIdPerson.id AND
		TbIdGrpPerson.groupId=TbIdGrp.id AND
		TbIdGrp.name='friends'
	)
'''
results=myworld.db.get_results(conn, sql)
for result in results:
	f_id=result['id']
	print('\t{0}'.format(result))
	sql2='''SELECT TbWkWork.name
		FROM TbWkWork, TbWkWorkContrib
		WHERE TbWkWork.id=TbWkWorkContrib.workId AND
		TbWkWorkContrib.personId='''+str(f_id)
	results2=myworld.db.get_results(conn, sql2)
	for result2 in results2:
		print('\t\t{0}'.format(result2))

print('checking for works that do not have external ids')
sql='''
SELECT
	TbWkWork.id, TbWkWork.name
FROM
	TbWkWork
WHERE
	NOT EXISTS (
		SELECT TbWkWorkExternal.id
		FROM TbWkWorkExternal
		WHERE TbWkWorkExternal.workId=TbWkWork.id
	)
'''
results=myworld.db.get_results(conn, sql)
for result in results:
	print('\t{0}'.format(result))

conn.close()
