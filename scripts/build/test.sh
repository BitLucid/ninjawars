#!/bin/bash
# This script is used to prepare and run the test
#
# Run it :
# bash scripts/build/test.sh

# Include functions
_DIR_=`dirname $0`
source $_DIR_/functions.sh

say_loud "Preparing..." "TEST"
bash $_DIR_/selenium.sh restart
say_loud "Waiting..." "TEST"
sleep 20 
say_info "Assuming finished." "TEST"
say_loud "Running test-suite" "TEST"
vendor/bin/phpunit