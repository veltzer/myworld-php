#!/bin/bash

# this script will create symlinks so that you could access a piece by it's name only...

# remove all old links...
rm -f by_title_name/*
for x in by_name/*/* ; do
	if [[ -d $x ]]; then
		y=`basename "$x"`
		ln -s "../$x" "by_title_name/$y"
	fi
done
