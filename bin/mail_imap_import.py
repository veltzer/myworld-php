#!/usr/bin/python3

'''
This script imports mail in maildir format to an imap server.

Restructuring the flow of this app:
- parse parameters from the command line.
- login to imap.
- recursively traverse the folder given.
- any file which is an email copy to gmail.

TODO:
- move to argparser
- do progress report.
- do watchdog for connections hanging.
- make the 'imap_import' tag be a parameter.
- make an rmdir executable to remove a directory on the imap server.
'''

import configparser # for ConfigParser
import os.path # for expanduser
import optparse # for OptionParser
import imap.imap # for IMAP

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

imp=imap.imap.IMAP()

imp.connect(opt_hostname, opt_port)
imp.login(opt_username, opt_password)

#imp.test(imap)
imp.import_folder(os.path.expanduser(options.mailfolder), opt_toplevel, options.progress)

imp.logout()
