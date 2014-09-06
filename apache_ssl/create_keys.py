#!/usr/bin/python3

import os # for unlink
import os.path # for isfile
import subprocess # for check

def remove_if_exists(f):
	if os.path.isfile('veltzer.key'):
		os.unlink('veltzer.key')
def check_exists(f):
	if not os.path.isfile(f):
		raise ValueError('must have file [{0}]'.format(f))

check_exists('privkey.pem')
check_exists('veltzer.pem.csr')
remove_if_exists('veltzer.key')
remove_if_exists('veltzer.pem')

print('you have to answer the following question to unlock the key...')
subprocess.check_call([
	'openssl',
	'rsa',
	'-in',
	'privkey.pem',
	'-out',
	'veltzer.key',
])
subprocess.check_call([
	'openssl',
	'x509',
	'-in',
	'veltzer.pem.csr',
	'-out',
	'veltzer.pem',
	'-req',
	'-signkey',
	'veltzer.key',
	'-days',
	'2000',
])
