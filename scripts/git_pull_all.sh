#!/bin/bash

for x in *; do
	if [[ -d "$x/.git" ]]; then
		if [[ ! -f "$x/.skip" ]]; then
			echo "doing [$x]"
			cd $x
			#git pull --tags
			git pull
			cd ..
		else
			echo "skipping [$x]"
		fi
	fi
done
