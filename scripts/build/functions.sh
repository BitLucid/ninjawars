#!/bin/bash
function quit {
	if [ -z "$1" ]
		then
		msg="Something went wrong!"
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
	# Capture label arg
	if [ "" == "$4" ]
		then
		LABEL="INSTALL_"$3
	else
		LABEL=$4"_"$3
	fi
	echo "$(tput bold)$(tput setaf $2)[$LABEL] $1"
	echo -en '\e[0m';
}

#just wrapper functions
function say_loud {
	_say "$1" 5 PHASE "$2"
}

function say_ok {
	_say "$1" 2 OK "$2"
}

function say_info {
	_say "$1" 4 INFO "$2"
}

function say_warning {
	_say "$1" 3 WARNING "$2"
}

function say_error {
	_say "$1" 1 ERROR "$2"
}

function check_package {
	say_info "Checking for $1" "$2"
	PKG_OK=$(dpkg-query -W --showformat='${Status}\n' $1|grep "install ok installed")
	if [ "" == "$PKG_OK" ]; then
		say_warning "$1 is not installed yet, installing..." "$2"
		sudo apt-get install $1
	else
		say_ok "$1 installed" "$2"
	fi
}


function ensure_system {
	# Note that travis itself require use of php fpm, annoyingly.
	echo "Just installing the various packages needed, apache, php, and php-fpm, etc"
	sudo apt-get update -qq
	sudo apt-get install apache2 libapache2-mod-fastcgi php5 php5-curl php5-pgsql postgresql postgresql-contrib 
	sudo apt-get install libxml2-dev openssl libpq-dev perl liblingua-en-inflect-perl libpcre3-dev
}

function ensure_phar {
	say_info "Checking for Phar module before installing Composer"
	PHAR_OK=$(php -m|grep "Phar")
	if [ "" == "$PHAR_OK" ]; then
		say_warning "Phar is not loaded!"
		say_info "Installing..."
		sudo apt-get install php-pear php5-dev 
		sudo pecl install phar
		sudo service apache2 restart
	else
		say_ok "Phar loaded!"
	fi
}

function ensure_curl {
	say_info "Checking for cURL module before installing Composer"
	CURL_OK=$(php -m|grep "curl")
	if [ "" == "$CURL_OK" ]; then
		say_warning "cURL is not loaded!"
		say_info "Installing..."
		sudo apt-get install php-pear php5-dev php5-curl
		sudo pecl install curl
		sudo service apache2 restart
	else
		say_ok "cURL loaded!"
	fi
}

function ensure_selenium {
	# Used selenium-server
	SELENIUM="selenium-server-standalone-2.33.0.jar"

	# Check java environment
	say_info "Checking for Java..." "SELENIUM"

	HAS_JAVA=$(file $(which java javac) | grep /usr/bin/java: | awk '{split($0,array," ")} END{print array[1]}')
	if [ "/usr/bin/java:" != "$HAS_JAVA" ]; then
		say_warning "Java platform not found, installing..." "SELENIUM"
		check_package openjdk-6-jre "SELENIUM"
		check_package openjdk-6-jdk "SELENIUM"
	else
		say_ok "Java platform in place" "SELENIUM"
	fi

	# Check selenium server
	say_info "Checking for Selenium..." "SELENIUM"

	sudo mkdir -p /usr/lib/selenium

	SELENIUM_OK=$(ls /usr/lib/selenium|grep $SELENIUM)
	if [ "" == "$SELENIUM_OK" ]; then
		say_warning "Selenium wasn't found, installing..." "SELENIUM"
		wget http://selenium.googlecode.com/files/$SELENIUM
		sudo cp $SELENIUM /usr/lib/selenium/$SELENIUM
	else
		say_ok "Selenium in place" "SELENIUM"
	fi
}

function set_composer {
	curl -s http://getcomposer.org/installer | php
	echo "Checking for presence of openssl "
	sudo apt-cache policy openssl
	php composer.phar install
}

function set_build {
	sed "s/postgres/$1/;s/nw/$2/" build.properties.tpl > build.properties
	sed "s/postgres/$1/;s/nw/$2/" buildtime.xml.tpl > buildtime.xml
	sed "s/postgres/$1/;s/nw/$2/" connection.xml.tpl > connection.xml
}

function set_webserver {
	say_info "Setting up web-server"
	FULL_SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" # get current directory of the script, without trailing slash
	DIR="$(echo $FULL_SCRIPT_DIR | sed 's/scripts\/build//')" # remove /scripts/build/ to get the repo directory, has trailing slash.
	echo "FULL_SCRIPT_DIR is found to be:", $FULL_SCRIPT_DIR # e.g. /home/travis/BitLucid/ninjawars/scripts/build
	echo "DIR is found to be:", $DIR # e.g. /home/travis/BitLucid/ninjawars/
	echo "TRAVIS_BUILD_DIR is found to be:", $TRAVIS_BUILD_DIR #e.g. /home/travis/BitLucid/ninjawars
	sudo bash -c "echo '127.0.0.1       nw.local' >> /etc/hosts"
	# enable php-fpm
	sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
	sudo a2enmod rewrite actions fastcgi alias
	#cgi isn't usually passed pathinfo, but this allows it to get it.
	echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
	# configure apache virtual hosts
	CONF_FILE=${TRAVIS_BUILD_DIR}/scripts/build/tpl/nw.local.travis.fpm.conf
	sudo cp -f $CONF_FILE /etc/apache2/sites-available/default
	sudo sed -e "s,%TRAVIS_BUILD_DIR%,$TRAVIS_BUILD_DIR,g" --in-place /etc/apache2/sites-available/default




	sudo a2ensite default
	sudo service apache2 restart
	cd $DIR
	# Replace user and database name in resources
	sed "s,__DBUSER__,$1,;s,__DBNAME__,$2," ${DIR}scripts/build/tpl/resources.php > ${DIR}deploy/resources.php

	if [[ ! -f ${DIR}deploy/resources.php ]]; then
	    echo "Resources file not properly deployed!"
	fi

	# Make the directories and give them all permissions
	mkdir -p ${DIR}deploy/templates/compiled ${DIR}deploy/templates/cache
	sudo chown www-data ${DIR}deploy/templates/compiled ${DIR}deploy/templates/cache
	sudo chmod 777 ${DIR}deploy/templates/compiled ${DIR}deploy/templates/cache
	echo "Outputting the title of the nw.local page if found"
	wget -qO- 'nw.local' | perl -l -0777 -ne 'print $1 if /<title.*?>\s*(.*?)\s*<\/title/si'
	say_ok "Web-server configured!"
}

#Have curl hit a url and return error on anything other than 
function curl_http_ok {
	#Second argument will prevent exit on fail
	response=$(curl --write-out %{http_code} --silent --output /dev/null $1)
	echo "Response http status: ", $response
	if [ 200 == $response ]; then
		return 0; #Ok
	else
		echo $response, " url fails!"
		return 1; #Error
	fi
}

#Have curl hit a url and return a WARNING on anything other than 200 status
function curl_http_out_warn {
	#Second argument will prevent exit on fail
	# IP=$(curl automation.whatismyip.com/n09230945.asp)
	code=$(curl --write-out %{http_code} --silent --output /dev/null $1)
	response=$(curl $1)
	echo "Response http status: ", $code
	if [ 200 == $code ]; then
		return 0; #Ok
	else
		echo "url fails, here was the output:\n"
		echo $response
		echo "End of Curl output"
		return 0; #Ok, continue execution even if the whole stack isn't quite there.
	fi
}
