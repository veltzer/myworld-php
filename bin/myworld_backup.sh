#!/bin/sh

# DESCRIPTION
# This script backs up any non home dir file related to my data.
# This means:
# - the myworld and wordpress databases.
# - the /var/www/blog folder.

# TODO
# - add backup of the entire web folder.

# PARAMETERS
FOLDER=`date +%y%m%d%H%M%S`
DIR=~/Dropbox.real/db/$FOLDER
DBS="--databases myworld"
DB="--database=myworld"
MYSQL_DATE=`date +'%F %T'`

# BODY
# insert the data of this backup into the database
mysql $DB --execute="INSERT INTO TbBackup (backupDate) values('$MYSQL_DATE')"
# lets make sure we have the output dir
mkdir $DIR
#OPTS="--complete-insert --skip-dump-date"
OPTS="--complete-insert"
# data and schema
mysqldump $OPTS $DBS | gzip > $DIR/data_and_schema.sql.gz
# only data
#mysqldump $OPTS $DB --no-create-info | gzip > $DIR/data_only.sql.gz
# only schema
mysqldump $OPTS $DBS --no-data | gzip > $DIR/schema_only.sql.gz
# only stats
mysql_stats.sh | gzip > $DIR/stats.txt.gz

# backup the blog
mysqldump $OPTS --databases wordpress | gzip > $DIR/wordpress_data_and_schema.sql.gz
tar jcfP $DIR/blog.tar.bz2 /var/www/blog
