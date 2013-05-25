#!/bin/sh
# This script is used only ONCE
# to integrate current running system
# with new architecture
#
# Run it :
# bash scripts/build/integration.sh <current_db_username>

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

say_loud "Checking for project depedencies"

ensure_phar
ensure_curl
set_composer
set_build $DBUSER
vendor/bin/propel-gen
psql -c 'ALTER TABLE "account_players" DROP CONSTRAINT "account_players_pkey";' -d nw -U $DBUSER
vendor/bin/propel-gen . diff migrate
vendor/bin/propel-gen . diff migrate

say_loud "Complete!"