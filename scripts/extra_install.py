#!/usr/bin/python3

'''
This script will install extra packages which cannot
be installed via the package manager
'''

import subprocess # for check_call

subprocess.check_call([
	'pip',
	'install',
	'--user',
	'--upgrade',
	'PyGithub',
])
