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
- do progress report.
- do watchdog for connections hanging.
- make the 'imap_import' tag be a parameter.
- make an rmdir executable to remove a directory on the imap server.
'''

import imaplib # for IMAP4_SSL
import configparser # for ConfigParser
import os.path # for expanduser
import optparse # for OptionParser
import imap.imap # for connect, login, db_open, test, import_folder, logout, db_close

########
# code #
########
cp = configparser.ConfigParser()
cp.read(os.path.expanduser('~/.details.ini'))
opt_username = cp.get('google', 'username')
opt_password = cp.get('google_imap', 'password')
opt_hostname = cp.get('google_imap', 'hostname')
opt_port = cp.get('google_imap', 'port')
opt_toplevel = 'imap_import'
opt_database = None

parser = optparse.OptionParser(
	description=__doc__,
	usage='%prog [options]',
)
parser.add_option('-m', '--mailfolder', dest='mailfolder', default='~/Mail', help='Folder where mail is. [default: %default]')
parser.add_option('-d', '--debug', action='store_true', dest='debug', default=False, help='do you want to debug the script? [default: %default]')
parser.add_option('-p', '--progress', action='store_true', dest='progress', default=False, help='report progress [default: %default]')
(options, args) = parser.parse_args()

if options.debug:
	print('opt_username:', opt_username)
	print('opt_password:', opt_password)
	print('opt_hostname:', opt_hostname)
	print('opt_port:', opt_port)
	print('options.folder:', options.folder)
	print('options.debug:', options.debug)
	print('options.progress:', options.progress)

imap=imap.imap.connect(opt_hostname, opt_port)
imap.imap.login(opt_username, opt_password)
imap.imap.db_open()

#imap.imap.test(imap)
imap.imap.import_folder(imap, os.path.expanduser(options.mailfolder), options.progress)

imap.imap.logout(imap)
imap=None
imap.imap.db_close()
