#!/usr/bin/python3

'''
This script imports mail in maildir format to an imap server.

Restructuring the flow of this app:
- parse parameters from the command line.
- login to imap.
- recursively traverse the folder given.
- any file which is an email move to gmail.

To see the documentation of the API use: pydoc imaplib
This thing started from me wanting to import my old mail to gmail and seeing
this blog post: http://scott.yang.id.au/2009/01/migrate-emails-maildir-gmail.html

TODO:
- move to argparser
'''

import imaplib # for IMAP4_SSL
import configparser # for ConfigParser
import os.path # for expanduser
import optparse # for OptionParser
import email # for messsage_from_string
import email.utils # for parsedate_tz
import email.header # for decode_header
import time # for localtime, mktime

####################
# helper functions #
####################

def decode_header(value):
	result = []
	for v, c in email.header.decode_header(value):
		try:
			if c is None:
				v = v.decode()
			else:
				v = v.decode(c)
		except (UnicodeError, LookupError):
			v = v.decode('iso-8859-1')
		result.append(v)
	return u' '.join(result)

def parsedate(value):
	value = decode_header(value)
	value = email.utils.parsedate_tz(value)
	timestamp = time.mktime(tuple(value[:9]))
	if value[9]:
		timestamp -= time.timezone + value[9]
		if time.daylight:
			timestamp += 3600
	return time.localtime(timestamp)

def imap_login(imap, username, password):
	(res, l)=imap.login(username, password)
	if res!='OK':
		raise ValueError('could not login with error [{0}]'.format(res))

def imap_logout(imap):
	(res, l)=imap.logout()
	if res!='BYE':
		raise ValueError('could not logout with error [{0}]'.format(res))

'''
check if we have a single folder. if you pass 'a/b' it will check if you have a SINGLE
folder called 'a/b'...
'''
def imap_have(imap, name):
	(res, l)=imap.list(name)
	if res!='OK':
		raise ValueError('could not list [{0}]. error is [{1}]'.format(name, l[0].decode()))
	if len(l)==1 and l[0] is None:
		return False
	return True

'''
this function creates a single folder.
if the folder exists then it will throw an exception
'''
def imap_create(imap, name):
	(res, l)=imap.create(name)
	if res!='OK':
		raise ValueError('could not create [{0}]. error is [{1}]'.format(name, l[0].decode()))

'''
this function deletes a single folder.
If the folder doesn't exist then it will throw an exception
'''
def imap_delete(imap, name):
	(res, l)=imap.delete(name)
	if res!='OK':
		raise ValueError('could not delete [{0}]. error is [{1}]'.format(name, l[0].decode()))

'''
check that we have a full path. Returns boolean to indicate the state.
'''
def imap_have_fullpath(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		if not imap_have(imap, '/'.join(parts[:x])):
			return False
	return True

'''
create a full path of folders. strict.
'''
def imap_create_fullpath(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		imap_create(imap, '/'.join(parts[:x]))

'''
delete a full path of folders. strict.
'''
def imap_delete_fullpath(imap, path):
	parts=path.split('/')
	for x in range(len(parts), 0, -1):
		imap_delete(imap, '/'.join(parts[:x]))

'''
append a single message to a mailbox
'''
def imap_append(imap, mailbox, flags, date_time, message):
	(res, l)=imap.append(mailbox, flags, date_time, message)
	if res!='OK':
		raise ValueError('could not append to [{0}]. error is [{1}]'.format(mailbox, l[0].decode()))

def imap_append_file(imap, folder, flags, filename):
	content = open(filename, 'rb').read()
	message = email.message_from_string(content)
	timestamp = parsedate(message['date'])
	subject = decode_header(message['subject'])
	imap_append(imap, folder, flags, timestamp, content)

'''
test function
'''
def imap_test(imap, options):
	if options.debug:
		print(imap.capability())
		print(imap.list())

	# this works
	'''
	assert not imap_have(imap, 'dontexist')
	assert imap_have(imap, 'business')
	'''

	# this works
	'''
	# now we try to delete a folder which does not exist.
	# this should raise an error. If it doesn't then we need to
	# error
	have_error=False
	try:
		imap_delete(imap, 'dontexist')
	except ValueError as e:
		have_error=True
	assert have_error
	'''

	# this works
	'''
	imap_create(imap, 'foo')
	assert imap_have(imap, 'foo')
	imap_delete(imap, 'foo')
	assert not imap_have(imap, 'foo')
	'''

	# this works
	# this creates a label called 'foo/bar' and not two labels one within the other
	'''
	imap_create(imap, 'foo/bar')
	imap_delete(imap, 'foo/bar')
	'''

	# this works
	'''
	imap_create_fullpath(imap, 'foo/bar/zoo')
	assert imap_have(imap, 'foo')
	assert imap_have(imap, 'foo/bar')
	assert imap_have(imap, 'foo/bar/zoo')
	imap_delete_fullpath(imap, 'foo/bar/zoo')
	assert not imap_have(imap, 'foo')
	assert not imap_have(imap, 'foo/bar')
	assert not imap_have(imap, 'foo/bar/zoo')
	'''

	# this should fail
	'''
	imap_create(imap, 'business')
	'''

	# this works
	'''
	assert imap_have_fullpath(imap, 'business/hinbit/projects/smartbuild')
	'''

	filename='/home/mark/Mail/.hobbies.directory/blog/cur/1279466171.2097.5oTh7:2,S'

	# lets try this
	#imap_create_fullpath(imap, 'foo/bar/zoo')
	imap_append(imap, 'foo/bar/zoo', None, None, open(filename, 'rb').read())

########
# code #
########
cp = configparser.ConfigParser()
cp.read(os.path.expanduser('~/.details.ini'))
opt_username = cp.get('google', 'username')
opt_password = cp.get('google_imap', 'password')
opt_hostname = cp.get('google_imap', 'hostname')
opt_port = cp.get('google_imap', 'port')

parser = optparse.OptionParser(
	description=__doc__,
	usage='%prog [options]',
)
parser.add_option('-f', '--folder', dest='folder', default=None, help='Folder where mail is. [default: %default]')
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
imap_test(imap, options)
imap_logout(imap)
