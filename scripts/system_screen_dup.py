#!/usr/bin/python3

"""
this script sets the two screens attached to this machine
to the same resolution and cloned (1024x768)
I mainly use this script for teaching...
"""

"""
TODO
- turn this script into python
- clone the resolution of the two screens instead of using
the hardcoded 1024x768.
- make the script check if indeed there are two screens, and if so will
clone their resolutions.
- blog about this (I don't think people know how to do this programmatically...)
- add probing of the screen names programatically.
"""

import subprocess

subprocess.check_output([
	"xrandr",
	"--output","eDP1",
	"--mode","1024x768",
	"--pos","0x0",
	"--crtc","0",
])
subprocess.check_output([
	"xrandr",
	"--output","VGA1",
	"--mode","1024x768",
	"--pos","0x0",
	"--crtc","1",
])
# dont try this at home...
# xrandr --output VGA1 --rotate inverted
