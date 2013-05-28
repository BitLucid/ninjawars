#!/bin/sh
# This script is used as convinient method
# everytime we pulled out new commits

# Include functions
_DIR_="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source $_DIR_/functions.sh

# Register sigint handler
trap quit_gracefully SIGINT

# Synchronize Propel ORM class and entities
say_loud "Synchronizing..."

vendor/bin/propel-gen . diff migrate
vendor/bin/propel-gen om

say_loud "Complete!"