#!/usr/bin/python3

'''
This script converts files given to it to some encoding, utf_8 by default.
It auto-detects the current encoding, reads using the current
encoding and writes using the utf-encoding.
'''

import chardet # for detect
import sys # for argv

#charset='utf_8';
charset='UTF-8';
#charset='ascii';
write=True

# list all encodings that are supported by python
# CAVEAT: does not list encodings that do not have aliases
#import encodings.aliases # for aliases
#print(encodings.aliases.aliases.keys())
#exit(1)

for filename in sys.argv[1:]:
	print('doing file',filename)
	with open(filename,'r') as f:
		b=f.read()
		h=chardet.detect(b);
		origcharset=h['encoding']
		print('detect is',h['encoding'])
		new_content=b.decode(origcharset).encode(charset)
	if write:
		with open(filename,'w') as f:
			f.write(new_content)
