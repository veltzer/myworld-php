#!/usr/bin/python2

'''
This script prints all of your github projects.

NOTES:
- this script should be python2 because it relies on the github module which
is python2 only
'''

from __future__ import print_function
import os.path # for expanduser
import ConfigParser # for ConfigParser
import github # for Github

inifile=os.path.expanduser('~/.githubrc')
config=ConfigParser.ConfigParser()
config.read(inifile)
opt_login=config.get('github','login')
opt_pass=config.get('github','pass')
g=github.Github(opt_login, opt_pass)

for repo in g.get_user().get_repos():
	print(repo.name)
