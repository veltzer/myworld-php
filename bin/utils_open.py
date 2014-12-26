#!/usr/bin/python3

'''
This is a wrapper for gnome-open(1) which does not pollute the screen.
'''

import sys # for argv
import subprocess # for call, DEVNULL

args=['/usr/bin/gnome-open']
args.extend(sys.argv[1:])
ret=subprocess.call(args, stderr=subprocess.DEVNULL)
assert ret==0
