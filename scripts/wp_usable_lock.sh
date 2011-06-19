#!/bin/bash

source scripts/defs.shinc

sudo chown -R root.root ${BLOGDIR}{wp-admin,wp-content}
#sudo chmod 644 ${HTACCESS}
