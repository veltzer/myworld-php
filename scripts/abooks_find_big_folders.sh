#!/bin/bash

# this script will find folders with large amount of files in them...

find by_name -type d -exec sh -c 'set -- "$0"/*.mp3; [ $# -ge 70 ]' {} \; -print
