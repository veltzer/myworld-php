#!/bin/sh

# this script updates your system

if test $USER != 'root';then
	echo "run me as root..."
	exit 1
fi

apt-get update
apt-get dist-upgrade
