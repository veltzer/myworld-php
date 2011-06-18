#!/bin/bash

BLOGDIR=/var/www/blog/
HTACCESS=/var/www/.htaccess

sudo chown -R root.root ${BLOGDIR}{wp-admin,wp-content}
#sudo chmod 644 ${HTACCESS}
