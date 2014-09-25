#!/usr/bin/python3

'''
A simple script to download stuff from ted.com via the command line
'''
 
import download.ted # for download 
import sys # for stderr, exit

if len(sys.argv)<2:
	print('usage: ted_download.py [links]', file=sys.stderr)
	print('example: ted_download.py http://www.ted.com/talks/david_cameron.html', file=sys.stderr)
	sys.exit(1)
for link in sys.argv[1:]:
	download.ted.download(link)
