#!/usr/bin/python2

'''
This script updates all movies lengths from imdb.
Unlike the equivalent perl script it does not check the name of the film.

TODO:
- check the name of the movie too.
- exception handling is not good, when getting timeouts from imdb it updates the
length to NULL (which is wrong). Take care of this.
'''

from __future__ import print_function
import MySQLdb # for connect
import imdb # for IMDb
import sys # for stdout, getdefaultencoding
import re # for compile

##############
# parameters #
##############
p_do_progress=True

#############
# functions #
#############
'''
times return as one of the following three:
u'104'
u'Argentina:94'
u'Canada:90::(Toronto International Film Festival)'
u'118::(unrated version)'
'''
regs=[
    re.compile('^(\d+)$'),
    re.compile('^.+:(\d+)$'),
    re.compile('^.+:(\d+)::.+$'),
    re.compile('^(\d+)::.+$'),
]

def analyze_runtime(runtime):
    runtime.encode(out_encoding, 'replace')
    for reg in regs:
        m=reg.match(runtime)
        if m:
            mins=m.group(1)
            return float(mins)*60.0
    raise ValueError('didnt find a regexp to match', runtime)

def avg(l):
    return sum(l)/len(l)

def analyze_runtimes(runtimes):
    return avg([analyze_runtime(x) for x in runtimes])

def update_time(db, cursor, f_id, deduced_runtime):
    cursor.execute('UPDATE TbWkWork SET length=%s, updatedLengthDate=NOW() WHERE id=%s',(deduced_runtime, f_id))
    db.commit()

def update_check(db, cursor, f_id):
    cursor.execute('UPDATE TbWkWork SET updatedLengthDate=NOW() WHERE id=%s',(f_id,))
    db.commit()

def reset_lengths(db):
    cursor=db.cursor()
    cursor.execute('UPDATE TbWkWork SET updatedLengthDate=NULL WHERE TbWkWork.typeId=15')
    db.commit()
    cursor.close()

########
# code #
########
connection=imdb.IMDb()
out_encoding=sys.stdout.encoding or sys.getdefaultencoding()
# this will read from the [client] section of the file
# that means that that section must declare 'database', 'user' and 'password' attributes.
db=MySQLdb.connect(read_default_file='~/.myworld.cnf')

cursor=db.cursor()
c_update=db.cursor()
sql='SELECT externalCode,workId FROM TbWkWorkExternal'
cursor.execute(sql)
ids={}
for x in cursor:
    f_externalCode=x[0]
    f_workId=x[1]
    ids[f_workId]=f_externalCode

# all movies which have not been update for length
sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\') AND TbWkWork.updatedLengthDate IS NULL'
# all movies
#sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\')'
# all movies where length is unknown
#sql='SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length FROM TbWkWork,TbWkWorkType WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in (\'video movie\') AND TbWkWork.length is NULL'

cursor.execute(sql)
stat_count=0
for x in cursor:
    stat_count+=1
    f_id=x[0]
    f_name=x[1]
    f_length=x[2]
    f_external=ids[f_id]
    if p_do_progress:
        print('working on [{0}]...'.format(f_name))
    movie=connection.get_movie(f_external)
    info_runtime=movie.get('runtime')
    print('f_id: {0}'.format(f_id))
    print('f_name: {0}'.format(f_name))
    print('f_length: {0}'.format(f_length))
    print('info_runtime: {0}'.format(info_runtime))
    if info_runtime is None:
        update_check(db, c_update, f_id)
    else:
        deduced_runtime=analyze_runtimes(info_runtime)
        print('deduced_runtime: {0}'.format(deduced_runtime))
        if f_length is None:
            print('============================')
            print('new time is {0}...'.format(deduced_runtime))
            print('============================')
            update_time(db, c_update, f_id, deduced_runtime)
        else:
            if deduced_runtime>f_length:
                print('============================')
                print('updating {0} with {1}...'.format(f_length, deduced_runtime))
                print('============================')
                update_time(db, c_update, f_id, deduced_runtime)
            else:
                update_check(db, c_update, f_id)
cursor.close()
db.close()
print('stat_count is [{0}]'.format(stat_count))
