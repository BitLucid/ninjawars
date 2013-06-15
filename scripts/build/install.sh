#!/bin/bash
# This script is used only ONCE
# to setup fresh-install
#
# Run it :
# bash scripts/build/integration.sh <db_username> <db_name>

# Include functions
_DIR_="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source $_DIR_/functions.sh

# Register sigint handler
trap quit_gracefully SIGINT

# Capture DB user arg
if [ "" == "$1" ]
	then
	if [ "" == "$SUDO_USER" ]
		then
		DBUSER=$(whoami)
	else	
		DBUSER=$SUDO_USER
	fi
else
	DBUSER=$1
fi

# Capture DB name arg
if [ "" == "$2" ]
	then
	DBNAME="nw"
else
	DBNAME=$2
fi

# Full install scripts started...
say_loud "Checking for system depedencies"

ensure_system

say_loud "Creating database"

DATABASE_CREATED=$(psql -c "create database $DBNAME;" -U $DBUSER | grep "CREATE DATABASE")
if [ "CREATE DATABASE" != "$DATABASE_CREATED" ]; then
	say_error $DBUSER" was not a valid Postgres user, create "$DBUSER" first!"
	quit_gracefully
fi


say_loud "Checking for project depedencies"

set_webserver $DBUSER $DBNAME
ensure_phar
ensure_curl
set_composer
set_build $DBUSER $DBNAME
vendor/bin/propel-gen
vendor/bin/propel-gen insert-sql

# TODO : need more clean way to import gitignore.
ln -s docs/gitignoreSAMPLE .gitignore

say_loud "Complete!"