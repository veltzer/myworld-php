#!/bin/bash

# first check that we are in the right folder
if [[ ! -d by_name ]]; then
	echo "put me where by_name is..."
	exit 1
fi

# this checks that the files are of the right type
echo "Checking that all are mp3"
find by_name -mindepth 1 -type f -and -not -name "*.mp3"
# this checks for permissions other than 444
echo "Permission problems"
find by_name -mindepth 1 -type f -and -not -perm 444
