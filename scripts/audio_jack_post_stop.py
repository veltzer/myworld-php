#!/usr/bin/python

# imports
import os
import os.path

# configuration
do_midi_bridge=True
do_load_jack_module=True
do_route_jack=True
do_route_apps=True
rcfile=os.path.expanduser('~/.myjack_run')

# code
if do_midi_bridge:
	with open(rcfile,'r') as f:
		p1=int(f.readline().rstrip())
		p2=int(f.readline().rstrip())
	os.kill(p1,9)
	os.kill(p2,9)
