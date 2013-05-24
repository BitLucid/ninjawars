#!/bin/sh
function quit {
	if [ -z "$1" ]
		then
		msg="Something goes wrong!"
	else
		msg=$1
	fi
	say_error "$msg"
	exit
}

function quit_gracefully {
	quit "Aborted!"
}

function _say {
	echo "$(tput bold)$(tput setaf $2)[INSTALL_$3] $1"
	echo -en '\e[0m';
}

function say_loud {
	_say "$1" 5 PHASE
}

function say_ok {
	_say "$1" 2 OK
}

function say_info {
	_say "$1" 4 INFO
}

function say_warning {
	_say "$1" 3 WARNING
}

function say_error {
	_say "$1" 1 ERROR
}

function check_package {
	say_info "Checking for $1"
	PKG_OK=$(dpkg-query -W --showformat='${Status}\n' $1|grep "install ok installed")
	if [ "" == "$PKG_OK" ]; then
		say_warning "$1 is not installed yet, installing..."
		sudo apt-get install $1
	else
		say_ok "$1 installed"
	fi
}