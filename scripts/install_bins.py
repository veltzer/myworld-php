#!/usr/bin/python

'''
this script installs all binaries of the meta package as symbolic links
into ~/install/bin
'''

import os # for walk, getcwd, readlink
import os.path # for join, expanduser, abspath, islink

root_folder='scripts'
target_folder=os.path.expanduser('~/install/bin')
doit=True
debug=False

cwd=os.getcwd()
for file in os.listdir(target_folder):
	full=os.path.join(target_folder, file)
	if os.path.islink(full):
		link_target=os.path.realpath(full)
		if link_target.startswith(cwd):
			if doit:
				if debug:
					print('unlinking [{0}]'.format(full))
				os.unlink(full)
for root,dirs,files in os.walk(root_folder):
	for cfile in files:
		source=os.path.abspath(os.path.join(root, cfile))
		target=os.path.join(target_folder, cfile)
		if doit:
			if debug:
				print('symlinking [{0}], [{1}]'.format(source, target))
			os.symlink(source, target)
