#!/bin/bash

source scripts/defs.shinc

sudo chown -R www-data.www-data ${BLOGDIR}{wp-admin,wp-content}
#sudo chmod 666 ${HTACCESS}
