#!/bin/bash

source ~/.myworld.shinc

sudo chown -R www-data.www-data ${BLOGDIR}{wp-admin,wp-content}
#sudo chmod 666 ${HTACCESS}
