#!/bin/sh
# find folders with _2, _3, ...
find -mindepth 3 -maxdepth 3 -name "*_2" > err_2.txt
find -mindepth 3 -maxdepth 3 -name "*_3" >> err_2.txt
find -mindepth 3 -maxdepth 3 -name "*_4" >> err_2.txt
