#!/bin/sh

# this script updates your system

#if test $USER != 'root';then
#	echo "run me as root..."
#	exit 1
#fi

sudo apt-get update
sudo apt-get dist-upgrade
