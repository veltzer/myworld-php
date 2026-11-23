#!/bin/bash

# Any failing step, including any stage of a pipe, fails the whole script.
set -eu
set -o pipefail

# The block below documents what this script does.
: <<'COMMENT'

This script backs up any non home dir file related to my data.
This means:
- the myworld and wordpress databases.
- the /var/www/blog folder.

TODO
- add backup of the entire web folder.

COMMENT

# PARAMETERS
FOLDER="$(date +%y%m%d%H%M%S)"
DIR=~/insync/backups/db/"${FOLDER}"
# The backup is built in a staging folder and only moved to its real name when
# every step succeeded, so a failed run never leaves something that looks like
# a good backup. The staging folder is removed when the script fails.
STAGING="${DIR}.partial"
DBS=(--databases myworld)
DB="--database=myworld"
MYSQL_DATE="$(date +'%F %T')"

function cleanup() {
	rm -rf "${STAGING}"
}
trap cleanup EXIT

# BODY
# lets make sure we have the output dir
mkdir "${STAGING}"
#OPTS="--complete-insert --skip-dump-date"
OPTS="--complete-insert"
# data and schema
echo "dumping the database with schema"
mysqldump "${OPTS}" "${DBS[@]}" | gzip > "${STAGING}/data_and_schema.sql.gz"
# only data
#mysqldump $OPTS $DB --no-create-info | gzip > $DIR/data_only.sql.gz
# only schema
echo "dumping the schema"
mysqldump "${OPTS}" "${DBS[@]}" --no-data | gzip > "${STAGING}/schema_only.sql.gz"
# only stats
echo "dumping stats"
mysql_stats.sh | gzip > "${STAGING}/stats.txt.gz"

# backup the blog
echo "dumping wordpress blog"
mysqldump "${OPTS}" --databases wordpress | gzip > "${STAGING}/wordpress_data_and_schema.sql.gz"
echo "dumping wordpress folder"
tar jcfP "${STAGING}/blog.tar.bz2" /var/www/blog

# everything worked, publish the backup under its real name
mv "${STAGING}" "${DIR}"
# insert the data of this backup into the database, only for a backup that exists
echo "putting new backup data into the db"
mysql "${DB}" --execute="INSERT INTO TbBackup (backupDate) values('${MYSQL_DATE}')"
