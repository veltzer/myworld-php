#!/usr/bin/python

'''
this script installs all products of the meta package as symbolic links
into ~/install.
'''

import os # for walk, getcwd, readlink
import os.path # for join, expanduser, abspath, islink

# actually perform the actions?
doit=True
# print what we are doing?
debug=True

def file_gen(root_folder, recurse):
	if recurse:
		for root,dirs,files in os.walk(root_folder):
			yield root,dirs,files
	else:
		dirs=[]
		files=[]
		for file in os.listdir(root_folder):
			full=os.path.join(root_folder, file)
			if os.path.isdir(full):
				dirs.append(file)
			if os.path.isfile(full):
				files.append(file)
		yield root_folder,dirs,files

def install(root_folder, target_folder, recurse):
	target_folder=os.path.expanduser(target_folder)
	cwd=os.getcwd()
	if os.path.isdir(target_folder):
		for file in os.listdir(target_folder):
			full=os.path.join(target_folder, file)
			if os.path.islink(full):
				link_target=os.path.realpath(full)
				if link_target.startswith(cwd):
					if doit:
						if debug:
							print('unlinking [{0}]'.format(full))
						os.unlink(full)
	else:
		os.mkdir(target_folder)
	for root,dirs,files in file_gen(root_folder, recurse):
		for file in files:
			source=os.path.abspath(os.path.join(root, file))
			target=os.path.join(target_folder, file)
			if doit:
				if debug:
					print('symlinking [{0}], [{1}]'.format(source, target))
				os.symlink(source, target)
		for dir in dirs:
			source=os.path.abspath(os.path.join(root, dir))
			target=os.path.join(target_folder, dir)
			if doit:
				if debug:
					print('symlinking [{0}], [{1}]'.format(source, target))
				os.symlink(source, target)

install('scripts', '~/install/bin', False)
install('perl', '~/install/myperl', False)
install('python', '~/install/mypython', False)
