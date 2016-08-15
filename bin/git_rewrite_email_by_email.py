#!/usr/bin/python3

'''
This will allow you to update your email in a git repository.
'''

import subprocess # for check_call
import sys # for argv, stderr, exit

if len(sys.argv)!=1:
    print('{0}: usage: {0}'.format(sys.argv[0]), file=sys.stderr)
    sys.exit(1)

old_email='mark@veltzer.net'
new_email='mark.veltzer@gmail.com'
expr='''if [ "$GIT_COMMITTER_EMAIL" = "{old_email}" ];
then
	GIT_AUTHOR_EMAIL="{new_email}";
	git commit-tree "$@";
else
	git commit-tree "$@";
fi'''.format(**locals())
# --force, HEAD
args=['git','filter-branch','--commit-filter',expr]
subprocess.check_call(args)
