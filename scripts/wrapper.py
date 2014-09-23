#!/usr/bin/python3

'''
run any command line and do not emit it's standard
error or output unless there is an error
'''

import sys # for argv, exit
import subprocess # for check_output, Popen

# this function is here because python2.6 does not have subprocess.check_output
def system_check_output(args):
	pr=subprocess.Popen(args,stdout=subprocess.PIPE,stderr=subprocess.PIPE)
	(output,errout)=pr.communicate()
	status=pr.returncode
	if status:
		print(output)
		print(errout)
		sys.exit(status);
		#raise ValueError('error in executing',args)

if len(sys.argv)<1:
	raise ValueError('command line issue')

# run the command
subprocess.check_output(sys.argv[1:])
