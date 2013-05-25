#!/bin/bash
# This script is used to prepare and run the test
#
# Run it :
# bash scripts/build/test.sh

# Include functions
_DIR_=`dirname $0`
source $_DIR_/functions.sh

say_loud "Preparing..." "TEST"
bash $_DIR_/selenium.sh start
sleep 8
say_loud "Running test-suite" "TEST"
vendor/bin/phpunit