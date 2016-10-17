#!/usr/bin/python3

'''
This script runs 'make' (make(1)) in every project
that is mine.
'''

import os.path # for expanduser, isdir, join
import utils.github # for get_nonforked_repos

home=os.getenv('HOME')

'''
projects=list()
filename=os.path.expanduser('~/.mrconfig')
for line in open(filename):
    line=line.rstrip()
    if line.startswith('['):
        project_root=os.path.join(home, line[1:-1])
        project_name=line[1:-1].split('/')[-1]
        projects.append((project_name, project_root))
'''

projects=[(repo.name, os.path.join(home,'git',repo.name)) for repo in utils.github.get_nonforked_repos_list()]

for project_name, project_root in projects:
    print('building [{0}] at [{1}]...'.format(project_name, project_root))
    makefile=os.path.join(project_root, 'Makefile')
    bootstrap=os.path.join(project_root, 'bootstrap')
    if os.path.isfile(makefile):
        os.chdir(project_root)
        os.system('make')
    elif os.path.isfile(bootstrap):
        os.chdir(project_root)
        os.system('./bootstrap')
    else:
        print('dont know how to build [{0}]...'.format(project_name))
