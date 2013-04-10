#!/bin/bash

for x in *; do
	if [[ -d "$x/.git" ]]; then
		echo "doing [$x]"
		cd $x
		git diff --name-only
		git status | grep "Changed but not updated"
		git status | grep "Your branch is ahead"
		git status | grep "Untracked files"
		cd ..
	fi
done
