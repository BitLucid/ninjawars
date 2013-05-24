#!/bin/sh

# Include functions
_DIR_=`dirname $0`
source $_DIR_/functions.sh

# Register sigint handler
trap quit_gracefully SIGINT

# Full install scripts started...
say_loud "Checking for system depedencies"

ensure_system

say_loud "Checking for project depedencies"

ensure_phar
curl -s http://getcomposer.org/installer | php
php composer.phar install
cp build.properties.tpl build.properties
cp buildtime.xml.tpl buildtime.xml
cp connection.xml.tpl connection.xml
vendor/bin/propel-gen
vendor/bin/propel-gen . diff migrate

say_loud "Complete!"