#!/usr/bin/python3

'''
This script will check that names in imdb are the same as in my db.
This script will also check that directors are connected correctly to works.
'''

###########
# imports #
###########
import sys # for stdout, getdefaultencoding

import MySQLdb # for connect
import imdb # for IMDb

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
def update_check(db, cursor, f_id, tablename):
    """ Stamp checkedDate on a row and return 1 for the check counter. """
    sql=f'UPDATE {tablename} SET checkedDate=NOW() WHERE id=%s'
    vals=(f_id,)
    if p_do_db:
        cursor.execute(sql, vals)
        db.commit()
    if p_do_progress:
        print(f'execute {sql} with {vals}')
    return 1

def update_field(db, cursor, f_id, fieldname, value):
    """ Update a single TbIdPerson field and return 1 for the update counter. """
    sql=f'UPDATE TbIdPerson SET {fieldname}=%s WHERE id=%s'
    vals=(value, f_id)
    if p_do_db:
        cursor.execute(sql, vals)
        db.commit()
    if p_do_progress:
        print(f'execute {sql} with {vals}')
    return 1

def split_canonical_name(i_canonical_name):
    """ Split an imdb 'surname, firstname othername' into its parts. """
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
        i_surname=i_canonical_name.strip()
        i_othername=None
    return i_firstname, i_surname, i_othername

def check_people(db, cursor, c_update, connection, menu, out_encoding):
    """ Compare db person names against imdb and update mismatches. """
    stat_check=0
    stat_update=0
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
    cursor.execute(sql)
    for x in cursor:
        f_id, f_firstname, f_surname, f_othername, f_externalCode, f_peid=x
        if p_do_progress:
            print(f'f_id: {f_id}')
            for label, val in (('f_firstname', f_firstname), ('f_surname', f_surname),
                               ('f_othername', f_othername)):
                if val is not None:
                    print(f'{label}: {val.encode(out_encoding)}')
            print(f'f_externalCode: {f_externalCode.encode(out_encoding)}')
            print(f'f_peid: {f_peid}')
        i_person=connection.get_person(f_externalCode)
        i_firstname, i_surname, i_othername=split_canonical_name(i_person['canonical name'])
        if p_do_progress:
            for label, val in (('i_firstname', i_firstname), ('i_surname', i_surname),
                               ('i_othername', i_othername)):
                if val is not None:
                    print(f'{label}: {val.encode(out_encoding)}')
        for fieldname, ival, fval in (('firstname', i_firstname, f_firstname),
                                      ('surname', i_surname, f_surname),
                                      ('othername', i_othername, f_othername)):
            if ival!=fval and (not p_confirm or menu.select()):
                stat_update+=update_field(db, c_update, f_id, fieldname, ival)
        stat_check+=update_check(db, c_update, f_peid, 'TbIdPersonExternal')
        if p_do_progress:
            print('=========================================')
    return stat_check, stat_update

def check_directors(db, cursor, c_update, connection):
    """ Check that db directors match imdb directors for each work. """
    stat_check=0
    sql='''
    SELECT
        TbWkWorkExternal.externalCode AS workExternalCode,
        TbIdPersonExternal.externalCode AS personExternalCode,
        TbWkWorkContrib.id
    FROM
        TbExternalType, TbWkWorkExternal, TbWkWork, TbWkWorkContrib, TbIdPerson, TbIdPersonExternal, TbExternalType AS B, TbWkWorkContribType
    WHERE
        TbExternalType.name='imdb_title_id' AND
        TbWkWorkExternal.externalId=TbExternalType.id AND
        TbWkWorkExternal.workId=TbWkWork.id AND
        TbWkWork.id=TbWkWorkContrib.workId AND
        TbWkWorkContrib.personId=TbIdPerson.id AND
        TbWkWorkContrib.checkedDate IS NULL AND
        TbIdPerson.id=TbIdPersonExternal.personId AND
        TbIdPersonExternal.externalId=B.id AND
        B.name='imdb_person_id' AND
        TbWkWorkContrib.typeId=TbWkWorkContribType.id AND
        TbWkWorkContribType.slug='movie_director'
    '''
    cursor.execute(sql)
    for x in cursor:
        f_workExternalCode, f_personExternalCode, f_id=x
        if p_do_progress:
            print(f'f_workExternalCode: {f_workExternalCode}')
            print(f'f_personExternalCode: {f_personExternalCode}')
            print(f'f_id: {f_id}')
        i_movie=connection.get_movie(f_workExternalCode)
        directors_set={d.personID for d in i_movie.get('director')}
        if p_do_progress:
            print(f'directors_set: {directors_set}')
        if f_personExternalCode in directors_set:
            stat_check+=update_check(db, c_update, f_id, 'TbWkWorkContrib')
    return stat_check

def main():
    """ main entry point """
    db=MySQLdb.connect(read_default_file='~/.myworld.cnf')
    cursor=db.cursor()
    c_update=db.cursor()
    menu=myworld.menu_maker.YNMenu('change ?')
    out_encoding=sys.stdout.encoding or sys.getdefaultencoding()
    connection=imdb.IMDb()

    stat_check, stat_update=check_people(db, cursor, c_update, connection, menu, out_encoding)
    stat_check+=check_directors(db, cursor, c_update, connection)

    cursor.close()
    db.close()

    print(f'stat_check is [{stat_check}]')
    print(f'stat_update is [{stat_update}]')

if __name__=='__main__':
    main()
