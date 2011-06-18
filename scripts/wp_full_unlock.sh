#!/bin/bash

# this script should be used when you are updating wordpress itself...

BLOGDIR=/var/www/blog/
HTACCESS=/var/www/.htaccess

sudo chown -R www-data.www-data ${BLOGDIR}
#sudo chmod 666 ${HTACCESS}
