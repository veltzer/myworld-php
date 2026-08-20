#!/usr/bin/python3

'''
A generic import application to be hacked for various purposes of importing
data into the myworld system.
'''

import datetime  # for strptime
import enum  # for Enum

import myworld.db  # for connect

# parameters

DO_DB=True
DO_DEBUG=True

ALLOWED={
    'name',
    'url',
    'type',
    'date',
    'loc',
    'review',
    'rating',
}
MUSTHAVE={
    'name',
    'url',
    'type',
    'date',
    'loc',
}

class State(enum.Enum):
    """ Parser state while reading the import text file. """
    s_after=1
    s_in=2

def date_to_mysql(output):
    """ Parse a `date` command output string into a datetime. """
    parts=output.split()
    if int(parts[2])<10:
        parts[2]='0'+parts[2]
    output=' '.join(parts)
    fmt='%a %b %d %H:%M:%S %Z %Y'
    # the `date` output carries %Z but strptime yields a naive local
    # datetime, which is what this local import wants.
    return datetime.datetime.strptime(output, fmt)  # noqa: DTZ007

def add_entry(cur, attrib):
    """ Validate one parsed entry and insert its work/view/review rows. """
    keys_set=set(attrib.keys())
    if not keys_set.issubset(ALLOWED):
        print(ALLOWED)
        print(keys_set)
        print(attrib)
        raise ValueError('bad state 3')
    if not MUSTHAVE.issubset(keys_set):
        print(ALLOWED)
        print(keys_set)
        print(attrib)
        raise ValueError('bad state 4')
    if 'review' in attrib and 'rating' not in attrib:
        print(attrib)
        raise ValueError('bad state 5')
    if DO_DEBUG:
        print('ended entry', attrib)
    if not DO_DB:
        return
    f_name=attrib['name']
    f_url=attrib['url']
    f_type=attrib['type']
    f_loc=attrib['loc']
    f_date=date_to_mysql(attrib['date'])
    if f_type=='YOUTUBE':
        f_externalId=40
        f_externalCode=f_url.split('=')[1]
    else:
        f_externalId=16
        f_externalCode=f_url
    # insert the work
    cur.execute('INSERT INTO TbWkWork (name, typeId) VALUES(%s,%s)', (f_name, 12))
    p_workid=cur.lastrowid
    # insert the external id
    cur.execute('INSERT INTO TbWkWorkExternal (externalCode, externalId, workId) VALUES(%s,%s,%s)',
        (f_externalCode, f_externalId, p_workid))
    # insert the view
    if f_loc=='by myself at my computer at home':
        cur.execute('INSERT INTO TbWkWorkView (locationId, deviceId, workId, endViewDate) VALUES(%s,%s,%s,%s)',
            (2, 33, p_workid, f_date))
    else:
        cur.execute('INSERT INTO TbWkWorkView (locationId, deviceId, workId, endViewDate, remark) VALUES(%s,%s,%s,%s,%s)',
            (10, 11, p_workid, f_date, f_loc))
    p_viewid=cur.lastrowid
    cur.execute('INSERT INTO TbWkWorkViewPerson (viewId, viewerId) VALUES(%s,%s)',
        (p_viewid, 1))
    # insert the review
    if 'review' in attrib and 'rating' in attrib:
        cur.execute('INSERT INTO TbWkWorkReview (ratingId, review, reviewDate, workId, reviewerId) VALUES(%s,%s,%s,%s,%s)',
            (attrib['rating'], attrib['review'], f_date, p_workid, 1))

def parse_line(state, attrib, line, cur):
    """ Advance the parser one line; returns the new (state, attrib). """
    if state==State.s_after:
        if line.startswith('\t'):
            raise ValueError('bad state 1')
        attrib['name']=line
        return State.s_in, attrib
    # state is s_in
    if line.startswith('\t'):
        parts=line.split(':')
        key=parts[0].strip()
        val=':'.join(parts[1:]).strip()
        if key in attrib:
            raise ValueError('bad state 2')
        attrib[key]=val
        return state, attrib
    add_entry(cur, attrib)
    return State.s_in, {'name': line}

def main():
    """ main entry point """
    conn=None
    cur=None
    if DO_DB:
        conn=myworld.db.connect()
        cur=conn.cursor()
    state=State.s_after
    attrib={}
    with open('educational_movies_saw.txt', encoding='utf-8') as stream:
        for line in stream:
            state, attrib=parse_line(state, attrib, line.rstrip(), cur)
    add_entry(cur, attrib)
    if DO_DB:
        cur.close()
        conn.commit()
        conn.close()

if __name__=='__main__':
    main()
