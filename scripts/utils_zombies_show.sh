#!/bin/sh

# This is a small script to show you all the zombie processes
# on the current machie
#ps aux | awk '{ print $8 " " $2 }' | grep -w Z
ps axo stat,pid,ppid,comm | grep -w defunct
