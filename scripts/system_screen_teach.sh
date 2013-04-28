#!/bin/sh

# this script sets the two screens attached to this machine
# to the same resolution and cloned (1024x768)
# (this is for teaching)

# TODO: turn this script into python and change it's name
# and functionality to screen_clone: clone the resolution
# of the two screens. The script will check if indeed there
# are two screens, and if so will clone their resolutions.
# blog about this.

xrandr --output eDP1 --mode 1024x768 --pos 0x0
xrandr --output VGA1 --mode 1024x768 --pos 0x0
