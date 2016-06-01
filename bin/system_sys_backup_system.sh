#!/bin/bash

# This script backs up your /etc folder (where 90% of your configuration lives)
# and the selection of packages you are using...

# this script should not be run as root since it needs the user name to change
# the file ownership to...:)
if [[ $USER == 'root' ]];then
	echo "do not run me as root..."
	exit 1
fi

HOSTNAME=`hostname`
# we sudo since we are running as regular user and cannot enter into some folders...
sudo tar jcvf /tmp/etc.$HOSTNAME.tar.bz2 /etc
cp /tmp/etc.$HOSTNAME.tar.bz2 .
sudo rm /tmp/etc.$HOSTNAME.tar.bz2
# this step can be done by any user...
dpkg --get-selections  > dpkg_selections.$HOSTNAME.txt
update-alternatives --get-selections > alternatives.$HOSTNAME.txt
# again - this can be done only as root...
#sudo chown $USER.$USER etc.$HOSTNAME.tar.bz2 dpkg_selections.$HOSTNAME.txt
