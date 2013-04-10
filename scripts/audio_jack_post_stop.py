#!/usr/bin/python3

# imports
import sys
import os.path
sys.path.append(os.path.expanduser('~/install/mypython'))
import os
import jack_pulse_config

# configuration
jack_pulse_config.getConfig()
runfile=os.path.expanduser('~/.myjack_run')

# code
if jack_pulse_config.do_midi_bridge:
	with open(runfile,'r') as f:
		p1=int(f.readline().rstrip())
		p2=int(f.readline().rstrip())
	os.kill(p1,9)
	os.kill(p2,9)
