#!/bin/bash

let x=600
while [[ $x -gt 0 ]]; do
	espeak "there are $x second to end of exercise"
	#espeak "there are $x second to end of course"
	sleep 60
	let "x=x-60"
done
