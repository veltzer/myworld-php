#!/bin/bash

# this checks that the files are of the right type
#find . -type f -and -not \( -name "*.avi" -or -name "*.flv" -or -name "*.mp4" -or -name "*.mov" -or -name "*.ogg" -or -name "*.ogv" -or -name "*.wma" \)
find . -type f -and -not -name "*.avi"
