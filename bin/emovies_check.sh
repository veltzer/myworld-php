#!/bin/bash

# this checks that the files are of the right type
echo "Checking that all are of the right suffix"
find . -mindepth 2 -type f -and -not -name "*.avi" -and -not -name "*.m4v" -and -not -name "*.mp4" -and -not -name "*.mkv" -and -not -name "*.wmv"
# this checks for permissions other than 444
echo "Permission problems"
find . -mindepth 2 -type f -and -not -perm 444
echo "directory problems"
find by_name -mindepth 4 -type f
find by_name -mindepth 1 -maxdepth 2 -type f
