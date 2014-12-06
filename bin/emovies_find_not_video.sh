#!/bin/bash

# this checks that the files are of the right type
find . -mindepth 2 -and -type f -and -not -name "*.avi" -and -not -name "*.m4v" -and -not -name "*.mp4" -and -not -name "*.mkv" -and -not -name "*.wmv"
#find . -mindepth 2 -type f -and -not \( -name "*.avi" -or -name "*.flv" -or -name "*.mp4" -or -name "*.mov" -or -name "*.ogg" -or -name "*.ogv" -or -name "*.wma" -or -name "*.m4v" -or -name "*.mkv" \)
