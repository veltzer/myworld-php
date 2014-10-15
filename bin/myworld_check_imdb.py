#!/usr/bin/python2

'''
This script will check that names in imdb are the same as in my db.

NOTES:
- the script is written in python2 because we need the imdb module which is
only available for python2 in ubuntu.
'''

###########
# imports #
###########
from __future__ import print_function
import MySQLdb # for connect
import imdb # for IMDb
import sys # for stdout, getdefaultencoding
import myworld.menu_maker # for YNMenu

##############
# parameters #
##############
# show progress reports?
p_do_progress=True
# confirm changes?
p_confirm=False
# actually do db stuff?
p_do_db=True

#############
# functions #
#############
def update_check(db, cursor, f_id):
	global stat_check
	stat_check+=1
	sql='UPDATE TbIdPersonExternal SET checkedDate=NOW() WHERE id=%s'
	vals=(f_id,)
	if p_do_db:
		cursor.execute(sql, vals)
		db.commit()
	if p_do_progress:
		print('execute {0} with {1}'.format(sql, vals))

def update_field(db, cursor, f_id, fieldname, value):
	global stat_update
	stat_update+=1
	sql='UPDATE TbIdPerson SET {0}=%s WHERE id=%s'.format(fieldname)
	vals=(value, f_id)
	if p_do_db:
		cursor.execute(sql, vals)
		db.commit()
	if p_do_progress:
		print('execute {0} with {1}'.format(sql, vals))

########
# code #
########
params={
	'db': 'myworld',
	'read_default_file': '~/.my.cnf',
}
db=MySQLdb.connect(**params)
cursor=db.cursor()
c_update=db.cursor()
menu=myworld.menu_maker.YNMenu('change ?')
out_encoding=sys.stdout.encoding or sys.getdefaultencoding()

connection=imdb.IMDb()

# select all people that have imdb ids and their imdbids.
sql='''
SELECT
	TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.surname, TbIdPerson.othername, TbIdPersonExternal.externalCode, TbIdPersonExternal.id AS peid
FROM
	TbIdPerson, TbIdPersonExternal, TbExternalType
WHERE
	TbIdPerson.id=TbIdPersonExternal.personId AND
	TbIdPersonExternal.externalId=TbExternalType.id AND
	TbExternalType.name='imdb_person_id' AND
	TbIdPersonExternal.checkedDate IS NULL
'''

stat_check=0
stat_update=0
cursor.execute(sql)
for x in cursor:
	f_id=x[0]
	f_firstname=x[1]
	f_surname=x[2]
	f_othername=x[3]
	f_externalCode=x[4]
	f_peid=x[5]
	if p_do_progress:
		print('f_id: {0}'.format(f_id))
		if f_firstname is not None:
			print('f_firstname: {0}'.format(f_firstname.encode(out_encoding)))
		if f_surname is not None:
			print('f_surname: {0}'.format(f_surname.encode(out_encoding)))
		if f_othername is not None:
			print('f_othername: {0}'.format(f_othername.encode(out_encoding)))
		print('f_externalCode: {0}'.format(f_externalCode.encode(out_encoding)))
		print('f_peid: {0}'.format(f_peid))
	i_person=connection.get_person(f_externalCode)
	i_canonical_name=i_person['canonical name']
	if ',' in i_canonical_name:
		i_surname, i_firstname=i_canonical_name.split(',')
		i_firstname=i_firstname.strip()
		i_surname=i_surname.strip()
		if ' ' in i_firstname:
			i_firstname, i_othername=i_firstname.split(' ')
		else:
			i_othername=None
	else:
		i_firstname=None
		i_surname=i_canonical_name
		i_surname=i_surname.strip()
		i_othername=None
	if p_do_progress:
		if i_firstname is not None:
			print('i_firstname: {0}'.format(i_firstname.encode(out_encoding)))
		if i_surname is not None:
			print('i_surname: {0}'.format(i_surname.encode(out_encoding)))
		if i_othername is not None:
			print('i_othername: {0}'.format(i_othername.encode(out_encoding)))
	if i_firstname!=f_firstname:
		#print('diff in firstname {0}!={1}'.format(i_firstname.encode(out_encoding), f_firstname.encode(out_encoding)))
		if not p_confirm or menu.select():
			update_field(db, c_update, f_id, 'firstname', i_firstname)
	if i_surname!=f_surname:
		#print('diff in surname {0}!={1}'.format(i_surname.encode(out_encoding), f_surname.encode(out_encoding)))
		if not p_confirm or menu.select():
			update_field(db, c_update, f_id, 'surname', i_surname)
	if i_othername!=f_othername:
		#print('diff in othername {0}!={1}'.format(i_othername.encode(out_encoding), f_othername.encode(out_encoding)))
		if not p_confirm or menu.select():
			update_field(db, c_update, f_id, 'othername', i_othername)
	update_check(db, c_update, f_peid)
	if p_do_progress:
		print('=========================================')
cursor.close()
db.close()

print('stat_check is [{0}]'.format(stat_check))
print('stat_update is [{0}]'.format(stat_update))
