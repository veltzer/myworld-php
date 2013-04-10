#!/bin/bash

# TODO:
# - fixup the code so it will work with the suffixes list

# This code does not work because of incorrect expansion
# the list of suffixes we support for book content
#suff=(chm tar.bz2 pdf ps html dvi lit doc djvu zip rtf txt pdb mht)
#cmd=""
#for x in ${suff[*]}; do
#	cmd="$cmd -and -not -name '*.$x'"
#done
#echo $cmd
# this checks that the files are of the right type
#find . -mindepth 1 -type f $cmd

# this checks that the files are of the right type
echo "EXTENSION PROBLEMS"
find by_name by_title -mindepth 2 -type f -and -not -name "*.chm" -and -not -name "*.tar.bz2" -and -not -name "*.pdf" -and -not -name "*.ps" -and -not -name "*.html" -and -not -name "*.dvi" -and -not -name "*.lit" -and -not -name "*.doc" -and -not -name "*.djvu" -and -not -name "*.zip" -and -not -name "*.rtf" -and -not -name "*.txt" -and -not -name "*.pdb" -and -not -name "*.mht" -and -not -name "*.rar" -and -not -name "*.jpg" -and -not -name "*.js" -and -not -name "*.gif"
# this checks for permissions other than 444
echo "PERMISSION PROBLEMS"
find . -mindepth 2 -type f -and -not -perm 444
# Really to enable this someday and take care of the problems it creates...
#echo "DEPTH PROBLEMS"
#find . -mindepth 5 -type f
