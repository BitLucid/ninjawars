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
	echo "Installing various packages, apache, php, and php-fpm, etc"
	sudo apt-get update -qq
	sudo apt-get install apache2 libapache2-mod-fastcgi php5 php5-curl php5-pgsql postgresql postgresql-contrib 
	sudo apt-get install libxml2-dev openssl libpq-dev perl liblingua-en-inflect-perl libpcre3-dev

   # enable php-fpm
   sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
   sudo a2enmod rewrite actions fastcgi alias
   echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
   ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
   # configure apache virtual hosts
   sudo cp -f build/tpl/nw.local.travis.fpm.conf /etc/apache2/sites-available/default
   sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/default
   sudo service apache2 restart
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

	HAS_JAVA=$(file `which java javac` | grep /usr/bin/java: | awk '{split($0,array," ")} END{print array[1]}')
	if [ "/usr/bin/java:" != $HAS_JAVA ]; then
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
	sudo bash -c "echo '127.0.0.1       nw.local' >> /etc/hosts"
	FULL_SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
	DIR=`echo $FULL_SCRIPT_DIR | sed 's/scripts\/build//'`
	cd /etc/apache2/sites-available
	#replace __DIR__ in the apache conf with the appropriate directory.
	sudo sh -c "sed 's,__DIR__,$DIR,' '$FULL_SCRIPT_DIR/tpl/nw.local.travis.fpm.conf' > 'nw.local.conf'"
	sudo a2enmod rewrite actions fastcgi alias
	sudo a2ensite nw.local
	echo "Restarting apache service..."
	sudo service apache2 restart
	cd $DIR
	sed "s,__DBUSER__,$1,;s,__DBNAME__,$2," $FULL_SCRIPT_DIR/tpl/resources.php > $DIR"deploy/resources.php"
	mkdir -p $DIR"deploy/templates/compiled"
	sudo chown www-data $DIR"deploy/templates/compiled"
	sudo chmod 777 $DIR"deploy/templates/compiled"
	mkdir -p $DIR"deploy/templates/cache"
	sudo chown www-data $DIR"deploy/templates/cache"
	sudo chmod 777 $DIR"deploy/templates/cache"
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
