#!/usr/bin/python

# this scrip will install all the required packages that you need on
# ubuntu to compile and work with this package.

import subprocess

packs=[
	'yui-compressor', # for compressing javascript
]

args=['sudo','apt-get','install']
args.extend(packs)
subprocess.check_call(args)
