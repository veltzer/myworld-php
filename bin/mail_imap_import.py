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
- do real progress report - find number of files to be imported
	in advance and report on progress.
- do watchdog for connections hanging.
- make an rmdir executable to remove a directory on the imap server.
	(make it in this executable using subcommands of argparser).
'''

import configparser # for ConfigParser
import os.path # for expanduser
import optparse # for OptionParser
import imap.imap # for IMAP
import sys # for exit

########
# code #
########
cp = configparser.ConfigParser()
cp.read(os.path.expanduser('~/.details.ini'))
opt_username = cp.get('google', 'username')
opt_password = cp.get('google_imap', 'password')
opt_hostname = cp.get('google_imap', 'hostname')
opt_port = cp.get('google_imap', 'port')
opt_database = None

parser = optparse.OptionParser(
	description=__doc__,
	usage='%prog [options]',
)
parser.add_option('', '--mailfolder', dest='mailfolder', default='~/Mail', help='Folder where mail is. [default: %default]')
parser.add_option('', '--debug', action='store_true', dest='debug', default=False, help='do you want to debug the script? [default: %default]')
parser.add_option('', '--exit', action='store_true', dest='exit', default=False, help='exit after debug? [default: %default]')
parser.add_option('', '--noprogress', action='store_true', dest='noprogress', default=False, help='dont report progress [default: %default]')
parser.add_option('', '--toplevel', dest='toplevel', default='imap_import', help='default tag under which to import [default: %default]')
(options, args) = parser.parse_args()

if options.debug:
	print('opt_username:', opt_username)
	print('opt_password:', opt_password)
	print('opt_hostname:', opt_hostname)
	print('opt_port:', opt_port)
	print('options.mailfolder:', options.mailfolder)
	print('options.debug:', options.debug)
	print('options.noprogress:', options.noprogress)
	print('options.toplevel:', options.toplevel)
if options.exit:
	sys.exit(0)

imp=imap.imap.IMAP()

imp.connect(opt_hostname, opt_port)
imp.login(opt_username, opt_password)

#imp.test(imap)
imp.import_folder(os.path.expanduser(options.mailfolder), options.toplevel, not options.noprogress)

imp.logout()
