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

function ensure_system {
	check_package apache2
	check_package php5
	check_package php5-pgsql
	check_package postgresql
	check_package postgresql-contrib
	check_package libpq-dev
	check_package perl
	check_package liblingua-en-inflect-perl
	check_package smarty
	check_package libpcre3-dev
}

function ensure_phar {
	say_info "Checking for Phar module before installing Composer"
	PHAR_OK=$(php -m|grep "Phar")
	if [ "" == "$PHAR_OK" ]; then
		say_warning "Phar is not loaded!"
		sudo apt-get install php-pear php5-dev 
		sudo pecl install phar
		sudo service apache2 restart
	else
		say_ok "Phar loaded!"
	fi
}

function set_composer {
	curl -s http://getcomposer.org/installer | php
	php composer.phar install
}

function set_build {
	sed "s/postgres/$1/" build.properties.tpl > build.properties
	sed "s/postgres/$1/" buildtime.xml.tpl > buildtime.xml
	sed "s/postgres/$1/" connection.xml.tpl > connection.xml
}