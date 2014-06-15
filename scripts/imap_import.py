#!/usr/bin/python3

"""
This script imports mail in maildir format to an imap server.

To see the documentation of the API use: pydoc imaplib

		Mark Veltzer <mark@veltzer.net>
"""

from __future__ import print_function
import imaplib # for IMAP4_SSL
import configparser # for ConfigParser
import os.path # for expanduser

##############
# parameters #
##############
# do you want to debug the script?
p_debug=False

####################
# helper functions #
####################

def read_config(cp, filename):
	filename=os.path.expanduser(filename)
	if not os.path.isfile(filename):
		raise ValueError('do not have config file', filename)
	cp.read(filename)

def imap_have_mailbox(imap, name):
	(res, l)=imap.list(name)
	if res!='OK':
		raise ValueError('could not list', name)
	if len(l)==1 and l[0] is None:
		return False
	return True

def imap_create(imap, name):
	(res, l)=imap.create(name)
	if res!='OK':
		raise ValueError('could not create', name)

def imap_delete(imap, name):
	(res, l)=imap.delete(name)
	if res!='OK':
		raise ValueError('could not delete', name)

def imap_logout(imap):
	(res, l)=imap.logout()
	if res!='BYE':
		raise ValueError('could not logout', res, l)

def imap_login(imap, username, password):
	(res, l)=imap.login(username, password)
	if res!='OK':
		raise ValueError('could not login')

########
# code #
########
cp = configparser.ConfigParser()
read_config(cp, '~/.pyimap.ini')
opt_username = cp.get('imap', 'username')
opt_password = cp.get('imap', 'password')
opt_hostname = cp.get('imap', 'hostname')
opt_port = cp.get('imap', 'port')
if p_debug:
	print('opt_username:', opt_username)
	print('opt_password:', opt_password)
	print('opt_hostname:', opt_hostname)
	print('opt_port:', opt_port)

imap = imaplib.IMAP4_SSL(opt_hostname, opt_port)
imap_login(imap, opt_username, opt_password)
#print(imap.capability())
#print(imap.list())
if imap_have_mailbox(imap, 'foo'):
	raise ValueError('have mailbox foo')
if not imap_have_mailbox(imap, 'business'):
	raise ValueError('do not have mailbox business')
imap_create(imap, 'foo')
imap_delete(imap, 'foo')
imap_logout(imap)
