#!/usr/bin/python3

"""
This script imports mail in maildir format to an imap server.

Restructuring the flow of this app:
- parse parameters from the command line.
- login to imap.
- recursively traverse the folder given.
- any file which is an email move to gmail.

To see the documentation of the API use: pydoc imaplib
This thing started from me wanting to import my old mail to gmail and seeing
this blog post: http://scott.yang.id.au/2009/01/migrate-emails-maildir-gmail.html

		Mark Veltzer <mark@veltzer.net>
"""

from __future__ import print_function
import imaplib # for IMAP4_SSL
import configparser # for ConfigParser
import os.path # for expanduser
import optparse # for OptionParser

# version of this script
__version__ = '0.1'

##############
# parameters #
##############
# try not to have parameters (use the options below...)

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

'''
this function creates a folder.
'''
def imap_create(imap, name):
	(res, l)=imap.create(name)
	if res!='OK':
		raise ValueError('could not create', name)

def imap_delete(imap, name):
	(res, l)=imap.delete(name)
	if res!='OK':
		raise ValueError('could not delete', name)

def imap_create_fullpath(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		imap_create(imap, '/'.join(parts[:x]))

def imap_delete_fullpath(imap, path):
	parts=path.split('/')
	for x in range(len(parts), 0, -1):
		imap_delete(imap, '/'.join(parts[:x]))

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

parser = optparse.OptionParser(
	description=__doc__,
	usage='%prog [options]',
	version=__version__
)

parser.add_option('-f', '--folder', dest='folder', default=__file__, help='Folder to store the emails. [default: %default]')
parser.add_option('-d', '--debug', action='store_true', dest='debug', default=False, help='do you want to debug the script? [default: %default]')
(options, args) = parser.parse_args()

if options.debug:
	print('opt_username:', opt_username)
	# remarked for security reasons
	#print('opt_password:', opt_password)
	print('opt_hostname:', opt_hostname)
	print('opt_port:', opt_port)
	print('options.folder:', options.folder)
	print('options.debug:', options.debug)

imap = imaplib.IMAP4_SSL(opt_hostname, opt_port)
imap_login(imap, opt_username, opt_password)
#print(imap.capability())
#print(imap.list())
if imap_have_mailbox(imap, 'foo'):
	raise ValueError('have mailbox foo')
if not imap_have_mailbox(imap, 'business'):
	raise ValueError('do not have mailbox business')
imap_create_fullpath(imap, 'foo/bar/zoo')
imap_delete_fullpath(imap, 'foo/bar/zoo')
imap_logout(imap)
