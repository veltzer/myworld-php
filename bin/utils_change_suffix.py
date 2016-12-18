#!/usr/bin/python3

"""
An app to help to you change suffix of files
"""

import logging

logging.basicConfig()
logger = logging.getLogger()
logger.setLevel(logging.DEBUG)


def yield_files():
    for root, dirs, files in os.walk(root_folder):
        for file in files:
            full = os.path.join(root, file)
            yield full


if len(sys.argv)!=2:
    print('usage: {} [suffix]'.format(sys.argv[0])
    sys.exit(1)

for filename in yield_files():
    logger.debug(filename)
