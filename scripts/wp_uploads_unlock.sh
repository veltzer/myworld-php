#!/bin/bash

BLOGDIR=/var/www/blog/
HTACCESS=/var/www/.htaccess

sudo chown -R www-data.www-data $BLOGDIR/wp-content/uploads
