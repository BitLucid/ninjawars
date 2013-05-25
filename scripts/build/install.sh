#!/bin/sh
# This script is used only ONCE
# to setup fresh-install
#
# Run it :
# bash scripts/build/integration.sh <db_username>

# Include functions
_DIR_=`dirname $0`
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

# Full install scripts started...
say_loud "Checking for system depedencies"

ensure_system

say_loud "Creating database"

psql -c 'create database nw;' -U $DBUSER

say_loud "Checking for project depedencies"

cp docs/tchalvakSAMPLEresources.php deploy/resources.php
ensure_phar
ensure_curl
set_composer
set_build $DBUSER
vendor/bin/propel-gen
vendor/bin/propel-gen insert-sql

say_loud "Complete!"