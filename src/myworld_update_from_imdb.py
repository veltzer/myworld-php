#!/usr/bin/python3

'''
This script updates all movies lengths from imdb.
Unlike the equivalent perl script it does not check the name of the film.

TODO:
- check the name of the movie too.
- exception handling is not good, when getting timeouts from imdb it updates the
length to NULL (which is wrong). Take care of this.
'''

import re  # for compile

import imdb  # for IMDb
import MySQLdb  # for connect

##############
# parameters #
##############
p_do_progress=True

# times return as one of the following three:
#   u'104'
#   u'Argentina:94'
#   u'Canada:90::(Toronto International Film Festival)'
#   u'118::(unrated version)'
REGS=[
    re.compile(r'^(\d+)$'),
    re.compile(r'^.+:(\d+)$'),
    re.compile(r'^.+:(\d+)::.+$'),
    re.compile(r'^(\d+)::.+$'),
]

#############
# functions #
#############
def analyze_runtime(runtime):
    """ Extract the minutes from an imdb runtime string, in seconds. """
    for reg in REGS:
        m=reg.match(runtime)
        if m:
            return float(m.group(1))*60.0
    raise ValueError('didnt find a regexp to match', runtime)

def avg(values):
    """ Arithmetic mean of a sequence. """
    return sum(values)/len(values)

def analyze_runtimes(runtimes):
    """ Average of the analyzed runtimes. """
    return avg([analyze_runtime(x) for x in runtimes])

def update_time(db, cursor, f_id, deduced_runtime):
    """ Store a deduced length and stamp updatedLengthDate. """
    cursor.execute('UPDATE TbWkWork SET length=%s, updatedLengthDate=NOW() WHERE id=%s',
                   (deduced_runtime, f_id))
    db.commit()

def update_check(db, cursor, f_id):
    """ Stamp updatedLengthDate without changing length. """
    cursor.execute('UPDATE TbWkWork SET updatedLengthDate=NOW() WHERE id=%s', (f_id,))
    db.commit()

def load_external_ids(cursor):
    """ Map workId -> externalCode from TbWkWorkExternal. """
    cursor.execute('SELECT externalCode,workId FROM TbWkWorkExternal')
    ids={}
    for f_externalCode, f_workId in cursor:
        ids[f_workId]=f_externalCode
    return ids

def process_movie(connection, db, c_update, f_id, f_name, f_length, f_external):
    """ Fetch a movie's runtime from imdb and update the db row. """
    if p_do_progress:
        print(f'working on [{f_name}]...')
    movie=connection.get_movie(f_external)
    info_runtime=movie.get('runtime')
    print(f'f_id: {f_id}')
    print(f'f_name: {f_name}')
    print(f'f_length: {f_length}')
    print(f'info_runtime: {info_runtime}')
    if info_runtime is None:
        update_check(db, c_update, f_id)
        return
    deduced_runtime=analyze_runtimes(info_runtime)
    print(f'deduced_runtime: {deduced_runtime}')
    if f_length is None or deduced_runtime>f_length:
        print('============================')
        if f_length is None:
            print(f'new time is {deduced_runtime}...')
        else:
            print(f'updating {f_length} with {deduced_runtime}...')
        print('============================')
        update_time(db, c_update, f_id, deduced_runtime)
    else:
        update_check(db, c_update, f_id)

def main():
    """ main entry point """
    connection=imdb.IMDb()
    # this reads the [client] section of ~/.myworld.cnf, which must declare
    # 'database', 'user' and 'password'.
    db=MySQLdb.connect(read_default_file='~/.myworld.cnf')
    cursor=db.cursor()
    c_update=db.cursor()
    ids=load_external_ids(cursor)

    # all movies which have not been updated for length
    sql=("SELECT TbWkWork.id,TbWkWork.name,TbWkWork.length FROM TbWkWork,TbWkWorkType "
         "WHERE TbWkWork.typeId=TbWkWorkType.id AND TbWkWorkType.name in ('video movie') "
         "AND TbWkWork.updatedLengthDate IS NULL")
    cursor.execute(sql)
    stat_count=0
    for f_id, f_name, f_length in cursor:
        stat_count+=1
        process_movie(connection, db, c_update, f_id, f_name, f_length, ids[f_id])
    cursor.close()
    db.close()
    print(f'stat_count is [{stat_count}]')

if __name__=='__main__':
    main()
