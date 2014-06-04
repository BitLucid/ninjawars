#!/bin/bash
# This script is used to prepare and run the test
#
# Run it :
# bash scripts/build/test.sh

# Include functions
_DIR_="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source $_DIR_/functions.sh

say_loud "Preparing..." "TEST"
bash $_DIR_/selenium.sh start

# Idle
#say_loud "Waiting... [20 seconds]" "TEST"
#sleep 5 
#say_info "Waiting... [15 seconds]" "TEST"
#sleep 5 
say_info "Waiting... [10 seconds]" "TEST"
sleep 5 
say_info "Waiting... [5 seconds]" "TEST"
sleep 5 

# Run
say_info "Assuming finished." "TEST"
say_loud "Running test-suite" "TEST"
find . -name phpunit #Find the path to the phpunit file in the travis environment.
ls #List current directory for debugging purposes.
echo "Vendor directory:"
ls vendor/
echo "Vendor/bin directory:"
ls vendor/bin/
if [ ! -f ./vendor/bin/phpunit ]; then
	say_loud "Phpunit not found." "TEST"
    exit 1 
    #phpunit not present, so force a fail
fi
say_info "Phpunit found." "TEST"
./vendor/bin/phpunit
PHPUNIT_OUTCOME=$?



# Clean up
say_loud "Cleaning up..." "TEST"

# Idle
#say_loud "Waiting... [20 seconds]" "TEST"
#sleep 5 
#say_info "Waiting... [15 seconds]" "TEST"
#sleep 5 
say_info "Waiting... [10 seconds]" "TEST"
sleep 5 
say_info "Waiting... [5 seconds]" "TEST"
sleep 5 

# Close selenium
bash $_DIR_/selenium.sh stop
say_ok "Completed!" "TEST"
return $PHPUNIT_OUTCOME