#!/bin/bash

source scripts/defs.shinc

sudo chown -R root.root $BLOGDIR
sudo chown -R www-data.www-data $BLOGDIR/wp-content/uploads
