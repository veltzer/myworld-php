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
