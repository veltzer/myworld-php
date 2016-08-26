#!/usr/bin/python3

'''
This script runs 'make' (make(1)) in every project
that is mine.
'''

import os.path # for expanduser, isdir, join
import utils.github # for get_nonforked_repos

'''
have_mrconfig=set()
filename=os.path.expanduser('~/.mrconfig')
for line in open(filename):
    line=line.rstrip()
    if line.startswith('['):
        project=line[1:-1].split('/')[-1]
        have_mrconfig.add(project)
'''

home=os.getenv('HOME')

for repo in utils.github.get_nonforked_repos_list():
    project_root=os.path.join(home,'git',repo.name)
    print('cleaning [{0}]...'.format(repo.name))
    os.chdir(os.path.join(home,'git',repo.name))
    os.system('git clean -qffxd')
