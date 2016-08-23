'''
Module which allows to connect easily to my database without
storing user or password in my script.
'''

import mysql.connector # for Connect
import os.path # for isfile, expanduser
import configparser # for ConfigParser
import getpass # for getuser

'''
get the configuration, including user and password from the ~/.my.cnf
file of the user

if no such file exists then use sensible defaults
'''
def get_config():
    d={}
    inifile=os.path.expanduser('~/.my.cnf')
    if os.path.isfile(inifile):
        config=configparser.ConfigParser()
        config.read(inifile)
        if config.has_option('mysql', 'user'):
            d['user']=config.get('mysql', 'user')
        else:
            d['user']=getpass.getuser()
        if config.has_option('mysql', 'database'):
            d['database']=config.get('mysql', 'database')
        else:
            d['database']='mysql'
        if config.has_option('mysql', 'password'):
            d['password']=config.get('mysql', 'password')
        return d
    else:
        d['user']=getpass.getuser()
        d['database']='mysql'
        return d

def connect():
    return mysql.connector.Connect(**get_config())

'''
Special cursor to return dicts and not tuples
Reference: http://geert.vanderkelen.org/connectorpython-custom-cursors/
'''
class MySQLCursorDict(mysql.connector.cursor.MySQLCursor):
    def _row_to_python(self, rowdata, desc=None):
        row = super(MySQLCursorDict, self)._row_to_python(rowdata, desc)
        if row:
            return dict(zip(self.column_names, row))
        return None

'''
get a cursor of the dictionary type
'''
def get_cursor(conn):
    #return conn.cursor(cursor_class=MySQLCursorDict)
    return conn.cursor(dictionary=True)

'''
A generic function to print the results of a query on the screen
'''
def print_results(conn, sql):
    cursor=conn.cursor()
    cursor.execute(sql)
    columns = [column[0] for column in cursor.description]
    results = []
    for row in cursor.fetchall():
        results.append(dict(zip(columns, row)))
    cursor.close()
    print(results)

'''
A generic function to get results in hash format from the db
'''
def get_results(conn, sql):
    cursor=conn.cursor()
    cursor.execute(sql)
    columns = [column[0] for column in cursor.description]
    results = []
    for row in cursor.fetchall():
        results.append(dict(zip(columns, row)))
    cursor.close()
    return results
