#!/bin/sh

# this is a small script to find bad symbolic links and printout their
# names.
find . -type l -and -not -exec test -e {} \; -print
