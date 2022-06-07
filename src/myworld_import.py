#!/usr/bin/python3

'''
A generic import application t be hacked for various purposes of importing
data into the myworld system.
'''

import subprocess # for check_output, check_call
import sys # for exit
import os # for unlink
import myworld.db # for connect
import enum # for Enum
import datetime # for strptime

# parameters, classes, functions

doDb=True

doDebug=True

class State(enum.Enum):
    s_after=1,
    s_in=2

allowed=set([
    'name',
    'url',
    'type',
    'date',
    'loc',
    'review',
    'rating',
])
musthave=set([
    'name',
    'url',
    'type',
    'date',
    'loc',
]);

def date_to_mysql(output):
    parts=output.split()
    if int(parts[2])<10:
        parts[2]='0'+parts[2]
    output=' '.join(parts)
    format='%a %b %d %H:%M:%S %Z %Y'
    d=datetime.datetime.strptime(output, format)
    #print('d is [{0}]'.format(d))
    return d

def add_entry(attrib):
    keys_set=set(attrib.keys())
    if not keys_set.issubset(allowed):
        print(allowed)
        print(keys_set)
        print(attrib)
        raise ValueError('bad state 3')
    if not musthave.issubset(keys_set):
        print(allowed)
        print(keys_set)
        print(attrib)
        raise ValueError('bad state 4')
    if 'review' in attrib and not 'rating' in attrib:
        print(attrib)
        raise ValueError('bad state 5')
    if doDebug:
        print('ended entry', attrib)
    if doDb:
        f_name=attrib['name']
        f_url=attrib['url']
        f_type=attrib['type']
        f_date=attrib['date']
        f_loc=attrib['loc']
        f_date=date_to_mysql(f_date)
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
        p_externalid=cur.lastrowid
        # insert the view
        if f_loc=='by myself at my computer at home':
            locationId=2
            deviceId=33
            cur.execute('INSERT INTO TbWkWorkView (locationId, deviceId, workId, endViewDate) VALUES(%s,%s,%s,%s)',
                (locationId, deviceId, p_workid, f_date))
        else:
            locationId=10
            deviceId=11
            cur.execute('INSERT INTO TbWkWorkView (locationId, deviceId, workId, endViewDate, remark) VALUES(%s,%s,%s,%s,%s)',
                (locationId, deviceId, p_workid, f_date, f_loc))
        p_viewid=cur.lastrowid
        cur.execute('INSERT INTO TbWkWorkViewPerson (viewId, viewerId) VALUES(%s,%s)',
            (p_viewid, 1))
        # insert the review
        if 'review' in attrib and 'rating' in attrib:
            f_review=attrib['review']
            f_rating=attrib['rating']
            cur.execute('INSERT INTO TbWkWorkReview (ratingId, review, reviewDate, workId, reviewerId) VALUES(%s,%s,%s,%s,%s)',
                (f_rating, f_review, f_date, p_workid, 1))
            p_reviewid=cur.lastrowid

# code

if doDb:
    conn=myworld.db.connect()
    cur=conn.cursor()
state=State.s_after
attrib=dict()
for line in open('educational_movies_saw.txt'):
    line=line.rstrip()
    if state==State.s_after:
        if line.startswith('\t'):
            raise ValueError('bad state 1')
        else:
            attrib['name']=line
            state=State.s_in
    elif state==State.s_in:
        if line.startswith('\t'):
            parts=line.split(':')
            key=parts[0]
            val=':'.join(parts[1:])
            key=key.strip()
            val=val.strip()
            if key not in attrib:
                attrib[key]=val
            else:
                raise ValueError('bad state 2')
        else:
            add_entry(attrib)
            attrib=dict()
            attrib['name']=line
            state=State.s_in

add_entry(attrib)
if doDb:
    cur.close()
    conn.commit()
    conn.close()
