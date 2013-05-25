#!/bin/bash
# This script is used to control selenium service
#
# Run it :
# bash scripts/build/selenium.sh (start|stop|restart)

# Include functions
_DIR_=`dirname $0`
source $_DIR_/functions.sh

say_loud "Preparing..." "SELENIUM"
check_package openjdk-6-jre "SELENIUM"
check_package openjdk-6-jdk "SELENIUM"
ensure_selenium

case "${1:-''}" in
	'start')
		say_loud "Starting Selenium..." "SELENIUM"

		if test -f /tmp/selenium.pid
			then
			say_warning "Selenium is already running." "SELENIUM"
		else
			java -jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar > /var/log/selenium/selenium-output.log 2> /var/log/selenium/selenium-error.log & echo $! > /tmp/selenium.pid

			error=$?
			if test $error -gt 0
				then
				say_error "${bon}Error $error! Couldn't start Selenium!${boff}" "SELENIUM"
			else
				say_ok "Started" "SELENIUM"
				ps aux | grep 2.21
			fi
		fi
	;;
	'stop')
		say_loud "Stopping Selenium..." "SELENIUM"
		if test -f /tmp/selenium.pid
		then
			PID=`cat /tmp/selenium.pid`
			kill -3 $PID
			if kill -9 $PID ;
				then
				sleep 2
				test -f /tmp/selenium.pid && rm -f /tmp/selenium.pid
				say_ok "Stoped" "SELENIUM"
			else
				say_error "Selenium could not be stopped..." "SELENIUM"
			fi
		else
			say_warning "Selenium is not running." "SELENIUM"
		fi
		;;
	'restart')
		say_loud "Restarting Selenium..." "SELENIUM"
		if test -f /tmp/selenium.pid
			then
			kill -HUP `cat /tmp/selenium.pid`
			test -f /tmp/selenium.pid && rm -f /tmp/selenium.pid
			sleep 1
			java -jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar > /var/log/selenium/selenium-output.log 2> /var/log/selenium/selenium-error.log & echo $! > /tmp/selenium.pid
			say_ok "Restarted" "SELENIUM"
		else
			say_error "Selenium isn't running..." "SELENIUM"
		fi
		;;
	*)  # no parameter specified
		say_loud "Usage:
		bash $0 start|stop|restart" "SELENIUM"
		exit 1
	;;
esac