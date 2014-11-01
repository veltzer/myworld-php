'''
module to help with imap
'''

import imaplib # for IMAP4_SSL
import dbm.gnu # for open

import os.path # for expanduser, join, isfile, relpath, dirname, sep
import os # for walk
import email # for messsage_from_string
import email.utils # for parsedate_tz
import email.header # for decode_header
import time # for localtime, mktime, timezone

# db functions

def db_open():
	global opt_database
	dbname=os.path.expanduser('~/.imap_import.db')
	opt_database = dbm.gnu.open(dbname, 'c')
def db_have(filename):
	global opt_database
	return filename in opt_database
def db_mark(filename):
	global opt_database
	opt_database[filename]='1'
def db_close():
	global opt_database
	opt_database.close()

# general helpers

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

# imap functions

def connect(opt_hostname, opt_port):
	return imaplib.IMAP4_SSL(opt_hostname, opt_port)

def login(imap, username, password):
	(res, l)=imap.login(username, password)
	if res!='OK':
		raise ValueError('could not login with error [{0}]'.format(res))

def logout(imap):
	(res, l)=imap.logout()
	if res!='BYE':
		raise ValueError('could not logout with error [{0}]'.format(res))

'''
check if we have a single folder. if you pass 'a/b' it will check if you have a SINGLE
folder called 'a/b'...
'''
def have(imap, name):
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
def create(imap, name):
	(res, l)=imap.create(name)
	if res!='OK':
		raise ValueError('could not create [{0}]. error is [{1}]'.format(name, l[0].decode()))

'''
this function deletes a single folder.
If the folder doesn't exist then it will throw an exception
'''
def delete(imap, name):
	(res, l)=imap.delete(name)
	if res!='OK':
		raise ValueError('could not delete [{0}]. error is [{1}]'.format(name, l[0].decode()))

'''
check that we have a full path. Returns boolean to indicate the state.
'''
def have_fullpath(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		if not have(imap, '/'.join(parts[:x])):
			return False
	return True

'''
create a full path and remember which paths have been created in a set
'''
def create_fullpath_db(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		cur='/'.join(parts[:x])
		if not db_have(cur):
			if not have(imap, cur):
				create(imap, cur)
			db_mark(cur)

'''
create a full path of folders. strict.
'''
def create_fullpath(imap, path):
	parts=path.split('/')
	for x in range(1, len(parts)+1):
		create(imap, '/'.join(parts[:x]))

'''
delete a full path of folders. strict.
'''
def delete_fullpath(imap, path):
	parts=path.split('/')
	for x in range(len(parts), 0, -1):
		delete(imap, '/'.join(parts[:x]))

'''
append a single message to a mailbox
'''
def append(imap, mailbox, flags, date_time, message):
	(res, l)=imap.append(mailbox, flags, date_time, message)
	if res!='OK':
		raise ValueError('could not append to [{0}]. error is [{1}]'.format(mailbox, l[0].decode()))

def append_file(imap, folder, flags, filename):
	content = open(filename, 'rb').read()
	message = email.message_from_string(content)
	timestamp = parsedate(message['date'])
	subject = decode_header(message['subject'])
	append(imap, folder, flags, timestamp, content)

'''
test function
'''
def test(imap):
	# this works
	'''
	print(imap.capability())
	print(imap.list())
	'''

	# this works
	'''
	assert not have(imap, 'dontexist')
	assert have(imap, 'business')
	'''

	# this works
	'''
	# now we try to delete a folder which does not exist.
	# this should raise an error. If it doesn't then we need to
	# error
	have_error=False
	try:
		delete(imap, 'dontexist')
	except ValueError as e:
		have_error=True
	assert have_error
	'''

	# this works
	'''
	create(imap, 'foo')
	assert have(imap, 'foo')
	delete(imap, 'foo')
	assert not have(imap, 'foo')
	'''

	# this works
	# this creates a label called 'foo/bar' and not two labels one within the other
	'''
	create(imap, 'foo/bar')
	delete(imap, 'foo/bar')
	'''

	# this works
	'''
	create_fullpath(imap, 'foo/bar/zoo')
	assert have(imap, 'foo')
	assert have(imap, 'foo/bar')
	assert have(imap, 'foo/bar/zoo')
	delete_fullpath(imap, 'foo/bar/zoo')
	assert not have(imap, 'foo')
	assert not have(imap, 'foo/bar')
	assert not have(imap, 'foo/bar/zoo')
	'''

	# this should fail
	'''
	create(imap, 'business')
	'''

	# this works
	'''
	assert have_fullpath(imap, 'business/hinbit/projects/smartbuild')
	'''

	filename='/home/mark/Mail/.hobbies.directory/blog/cur/1279466171.2097.5oTh7:2,S'

	# lets try this
	#create_fullpath(imap, 'foo/bar/zoo')
	append(imap, 'foo/bar/zoo', None, None, open(filename, 'rb').read())

def import_folder(imap, folder, doprogress):
	for root, dir, files in os.walk(folder):
		for file in files:
			if not file.endswith(',S'):
				continue
			filename=os.path.join(root, file)
			assert os.path.isfile(filename)
			relpath=os.path.relpath(os.path.dirname(filename), folder)
			# calculate folder in gmail
			parts=relpath.split(os.path.sep)
			assert parts[-1]=='cur'
			parts.pop()
			# all but the last folder element are of the form [.folder.directory]
			for i, part in enumerate(parts[:-1]):
				assert part.endswith('.directory')
				assert part.startswith('.')
				parts[i]=part[1:-10]
			target_folder='/'.join([opt_toplevel, '/'.join(parts)])
			if doprogress:
				print('filename is [{0}]'.format(filename))
				print('target_folder is [{0}]'.format(target_folder))
			create_fullpath_db(imap, target_folder)
			if not db_have(filename):
				append(imap, target_folder, None, None, open(filename, 'rb').read())
				db_mark(filename)
