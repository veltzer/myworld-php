#!/usr/bin/env python

""" Build the WordPress plugin/theme zip bundles, reproducing the Makefile:
zip -r each source directory into out/. File arguments are ignored; the
directory->zip mapping below defines the build. """

import os
import subprocess
import sys

# source directory -> output zip
BUNDLES = {
    "myworld": os.path.join("out", "plugins", "myworld.zip"),
    "myheb": os.path.join("out", "plugins", "myheb.zip"),
    "mytheme": os.path.join("out", "themes", "mytheme.zip"),
}


def main():
    """ main entry point """
    for source, target in BUNDLES.items():
        os.makedirs(os.path.dirname(target), exist_ok=True)
        if os.path.exists(target):
            os.unlink(target)
        # zip stores paths relative to cwd, so run zip from the repo root with
        # the source dir name -- matching the Makefile's `zip -r $@ <name>`.
        ret = subprocess.call(["zip", "--quiet", "-r", target, source])
        if ret != 0:
            sys.exit(ret)


if __name__ == "__main__":
    main()
