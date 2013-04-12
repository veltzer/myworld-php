#!/bin/bash

let x=10
while [[ $x -gt 0 ]]; do
	echo $x
	sleep 1
	let "x=x-1"
	espeak "tthere are $x second to end of course"
done
