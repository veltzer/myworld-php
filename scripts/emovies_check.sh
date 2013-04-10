#!/bin/bash

# this checks that the files are of the right type
echo "Checking that all are of the right suffix"
find . -mindepth 2 -type f -and -not -name "*.avi"
# this checks for permissions other than 444
echo "Permission problems"
find . -mindepth 2 -type f -and -not -perm 444
