#!/usr/bin/python3

'''
This script checks the sanity of the myworld database.

here is what it should check:
1. AUTO_INCREMENT always implies tight packing of the table.
2. no works without views.
3. no works without reviews.
4. no works without lengths.
5. no person without single group representing him.
6. no works without people involved (at least one).
7. no person without external id of some sort (at least one).
8. check the reverse: that every file in the download folders corresponds to a work and external entity in my database.
9. that every audio book that has length has it's correct folder to calculate the length from.
'''

###########
# imports #
###########
import myworld.db # for connect, get_results
import os.path # for join, isdir

#############
# functions #
#############
def check_1(conn):
    print('checking for work views that have remarks or todos')
    sql='''SELECT TbWkWorkView.workId FROM TbWkWorkView WHERE remark IS NOT NULL OR todo IS NOT NULL'''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        print('\t{0}'.format(result))

def check_2(conn):
    print('checking works which are videos without length updated')
    sql='''
    SELECT
        TbWkWork.id, TbWkWork.name
    FROM
        TbWkWork, TbWkWorkType
    WHERE
        updatedLengthDate IS NULL
        AND TbWkWork.typeId=TbWkWorkType.id
        AND TbWkWorkType.isVideo=TRUE
    '''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        f_id=result['id']
        print('\t{0}'.format(result))
        sql2='''SELECT TbWkWorkExternal.externalCode, TbExternalType.name, TbWkWorkExternal.id
            FROM TbWkWorkExternal, TbExternalType
            WHERE TbExternalType.id=TbWkWorkExternal.externalId AND
            TbWkWorkExternal.workId='''+str(f_id)
        results2=myworld.db.get_results(conn, sql2)
        for result2 in results2:
            print('\t\t{0}'.format(result2))

def check_3(conn):
    print('checking works with space in the begining or end of name')
    sql='''
    SELECT
        TbWkWork.id, TbWkWork.name
    FROM
        TbWkWork
    WHERE
        name REGEXP '^[[:space:]].*$' OR
        name REGEXP '^.*[[:space:]]$'
    '''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        print('\t{0}'.format(result))

def check_4(conn):
    print('checking people with space in the firstname or othername')
    sql='''
    SELECT
        TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.othername
    FROM
        TbIdPerson
    WHERE
        firstname REGEXP '[[:space:]]' OR
        othername REGEXP '[[:space:]]'
    '''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        print('\t{0}'.format(result))

def check_5(conn):
    print('checking people with space in the begining or end of their firstname, surname or othername')
    sql='''
    SELECT
        TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.surname, TbIdPerson.othername
    FROM
        TbIdPerson
    WHERE
        firstname REGEXP '^[[:space:]].*$' OR
        firstname REGEXP '^.*[[:space:]]$' OR
        surname REGEXP '^[[:space:]].*$' OR
        surname REGEXP '^.*[[:space:]]$' OR
        othername REGEXP '^[[:space:]].*$' OR
        othername REGEXP '^.*[[:space:]]$'
    '''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        print('\t{0}'.format(result))

def check_6(conn):
    print('checking for people that are not connected to works and are not friends')
    sql='''
    SELECT
        TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.surname
    FROM
        TbIdPerson
    WHERE
        NOT EXISTS (
            SELECT TbWkWork.id
            FROM TbWkWork, TbWkWorkContrib
            WHERE TbWkWork.id=TbWkWorkContrib.workId AND
            TbWkWorkContrib.personId=TbIdPerson.id
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
        print('\t{0}'.format(result))

def check_7(conn):
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
        sql2='''SELECT TbWkWork.name, TbWkWork.id, TbWkWorkType.name as typeName
            FROM TbWkWork, TbWkWorkContrib, TbWkWorkType
            WHERE TbWkWork.id=TbWkWorkContrib.workId AND
            TbWkWorkType.id=TbWkWork.typeId AND
            TbWkWorkContrib.personId='''+str(f_id)
        results2=myworld.db.get_results(conn, sql2)
        for result2 in results2:
            f_workid=result2['id']
            print('\t\t{0}'.format(result2))
            sql3='''SELECT TbWkWorkExternal.externalCode, TbExternalType.name
            FROM TbWkWorkExternal, TbExternalType
            WHERE TbWkWorkExternal.externalId=TbExternalType.id AND
            TbWkWorkExternal.workId='''+str(f_workid)
            results3=myworld.db.get_results(conn, sql3)
            for result3 in results3:
                print('\t\t\t{0}'.format(result3))

def check_8(conn):
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
    ORDER BY TbWkWork.name
    '''
    results=myworld.db.get_results(conn, sql)
    for result in results:
        f_id=result['id']
        print('\t{0}'.format(result))
        sql2='''SELECT TbIdPerson.id, TbIdPerson.firstname, TbIdPerson.surname, TbWkWorkContribType.name
            FROM TbIdPerson, TbWkWorkContrib, TbWkWorkContribType
            WHERE TbIdPerson.id=TbWkWorkContrib.personId AND
            TbWkWorkContribType.id=TbWkWorkContrib.typeId AND
            TbWkWorkContrib.workId='''+str(f_id)
        results2=myworld.db.get_results(conn, sql2)
        for result2 in results2:
            print('\t\t{0}'.format(result2))

def check_9(conn):
    print('checking for audio works that do not have files')
    sql='''
    SELECT
        TbWkWork.id, TbWkWork.name
    FROM
        TbWkWork, TbWkWorkType
    WHERE
        TbWkWork.typeId=TbWkWorkType.id AND
        TbWkWorkType.isAudio=TRUE
    '''
    basedir='/home/mark/slow_links/topics_archive/audio/abooks/by_title_name'
    results=myworld.db.get_results(conn, sql)
    for result in results:
        f_name=result['name']
        folder=os.path.join(basedir, f_name)
        if not os.path.isdir(folder):
            print('\t{0}'.format(result))

########
# code #
########
conn=myworld.db.connect()
check_1(conn)
check_2(conn)
check_3(conn)
check_4(conn)
check_5(conn)
check_6(conn)
check_7(conn)
check_8(conn)
check_9(conn)
conn.close()
