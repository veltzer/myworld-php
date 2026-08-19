#!/usr/bin/python3

'''
this script installs all products of the meta package as symbolic links
into ~/install.

TODO:
- add easy option to copy files instead of symlinking them.
'''

import os # for walk, getcwd, symlink, listdir, unlink, mkdir
import os.path # for join, expanduser, realpath, abspath, islink, isdir, isfile

# actually perform the actions?
DOIT=True
# print what we are doing?
DEBUG=True
# remove target files if they are links
FORCE=True

def do_install(source, target):
    """ Symlink source at target, replacing an existing link if FORCE. """
    if FORCE:
        if os.path.islink(target):
            os.unlink(target)
    if DOIT:
        if DEBUG:
            print(f'symlinking [{source}], [{target}]')
        os.symlink(source, target)

def file_gen(root_folder, recurse):
    """ Yield (root, dirs, files) either recursively or for one level. """
    if recurse:
        yield from os.walk(root_folder)
    else:
        dirs=[]
        files=[]
        for file in os.listdir(root_folder):
            full=os.path.join(root_folder, file)
            if os.path.isdir(full):
                dirs.append(file)
            if os.path.isfile(full):
                files.append(file)
        yield root_folder, dirs, files

def clean_stale_links(target_folder, cwd):
    """ Remove links in target_folder that point back into cwd. """
    for file in os.listdir(target_folder):
        full=os.path.join(target_folder, file)
        if not os.path.islink(full):
            continue
        link_target=os.path.realpath(full)
        if link_target.startswith(cwd) and DOIT:
            if DEBUG:
                print(f'unlinking [{full}]')
            os.unlink(full)

def install(root_folder, target_folder, recurse):
    """ Symlink everything under root_folder into target_folder. """
    target_folder=os.path.expanduser(target_folder)
    if os.path.isdir(target_folder):
        clean_stale_links(target_folder, os.getcwd())
    else:
        os.mkdir(target_folder)
    for root, dirs, files in file_gen(root_folder, recurse):
        for name in files+dirs:
            source=os.path.abspath(os.path.join(root, name))
            target=os.path.join(target_folder, name)
            do_install(source, target)

install('src', '~/install/bin', False)
install('perl', '~/install/perl', False)
install('python', '~/install/python', False)
