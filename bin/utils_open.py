#!/usr/bin/python3

'''
This is a wrapper for gnome-open(1) which does not pollute the screen.
'''

import sys # for argv, exit
import subprocess # for call, DEVNULL

args=[
	'gnome-open',
]
# give all command line args passed to the wrapper excluding the first (the program name)
args.extend(sys.argv[1:])
sys.exit(subprocess.call(args, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL))
