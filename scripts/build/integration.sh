#!/bin/sh

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
set_composer
set_build $DBUSER
vendor/bin/propel-gen
psql -c 'ALTER TABLE "account_players" DROP CONSTRAINT "account_players_pkey";' -U $DBUSER
vendor/bin/propel-gen . diff migrate

say_loud "Complete!"