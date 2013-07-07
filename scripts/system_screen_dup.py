#!/usr/bin/python3

"""
This script checks that two screens are attached to the current
machine, and then sets the two screens to the maximum resolution
that the two screens support.
I mainly use this script for teaching...

	Mark Veltzer <mark@veltzer.net>

TODO
- blog about this (I don't think people know how to do this programmatically...)

NOTES:
- If you want to see something similar, look at /usr/bin/xrandr-tool.
- I found that the "crtc" parameter to xrandr was essential (did not work without it).
"""

import subprocess # for check_output
import re # for compile

# parameters
res=[
	(1280,1024),
	(1280,960),
	(1280,800),
	(1152,864),
	(1024,768),
	(832,624),
	(800,600),
	(720,400),
	(640,480),
]
real=True
checkTwoScreens=True
doPrint=True

class Output:
	def __init__(self,name):
		self.name=name
		self.resolutions=set()
	def addResolution(self,x,y):
		self.resolutions.add((x,y))

def run(args):
	if real:
		subprocess.check_output(args)
	else:
		print(args)
def find_highest_mode(outputs):
	for r in res:
		for output in outputs:
			if not r in output.resolutions:
				break
		else:
			return r
def find_all_outputs():
	outputs=[]
	# regular expresson to find disconnected|connected devices
	re_output = re.compile("^(.*) (?:disconnected|connected) (.*)")
	re_res = re.compile("^   (\d+)x(\d+) *(.*)")
	process = subprocess.Popen(['xrandr'], stdout=subprocess.PIPE)
	xrandr_stdout, xrandr_stderr = process.communicate()
	for line in str(xrandr_stdout, encoding='utf8').split("\n"):
		m = re_output.match(line)
		if m:
			current_output_name = m.group(1)
			o=Output(current_output_name)
			outputs.append(o)
		m = re_res.match(line)
		if m:
			o.addResolution(int(m.group(1)),int(m.group(2)))
	return outputs
def find_outputs():
	outputs=[]
	# regular expresson to find connected devices
	re_output = re.compile("^(.*) (?:connected) (.*)")
	re_res = re.compile("^   (\d+)x(\d+) *(.*)")
	process = subprocess.Popen(['xrandr'], stdout=subprocess.PIPE)
	xrandr_stdout, xrandr_stderr = process.communicate()
	for line in str(xrandr_stdout, encoding='utf8').split("\n"):
		m = re_output.match(line)
		if m:
			current_output_name = m.group(1)
			o=Output(current_output_name)
			outputs.append(o)
		m = re_res.match(line)
		if m:
			o.addResolution(int(m.group(1)),int(m.group(2)))
	return outputs

def set_outputs(outputs, mode):
	for index,output in enumerate(outputs):
		run([
			"xrandr",
			"--output",output.name,
			"--mode",str(mode[0])+"x"+str(mode[1]),
			"--pos","0x0",
			"--crtc",str(index),
		])
def invert_the_screen():
	# dont try this at home...
	run([
		"xrandr",
		"--output","VGA1",
		# this can be 'normal', 'left', 'right' or 'inverted'
		# see the xrandr(1) manual page
		"--rotate","inverted",
	])
outputs=find_outputs()
if checkTwoScreens and len(outputs)!=2:
	raise ValueError("you have !=2 screens connected")
if doPrint:
	print('found two screens')
mode=find_highest_mode(outputs)
if doPrint:
	print('found best mode to be',mode)
set_outputs(outputs, mode)
