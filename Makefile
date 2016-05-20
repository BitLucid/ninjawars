.PHONY: all ci pre-test test test-main test-integration test-unit test-quick test-functional test-js post-test clean dep build install install-system dist-clean db db-fixtures migration

DOMAIN=http://nw.local/
COMPOSER=./composer.phar
CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)
TEST_RUNNER=php -d zend_extension=xdebug.so ./vendor/bin/phpunit
RELATIVE_COMPONENTS=../components/
SRC:=`pwd`/deploy/
WWW=$(SRC)www/
COMPONENTS=$(WWW)components/
JS=$(WWW)js/
DBROLE=developers
PROPEL=./vendor/bin/propel-gen
NGINX_PATH:=`readlink -f nginx-1.9.12/objs/nginx`

-include CONFIG

ifdef NOCOVER
	CC_FLAG=
endif

ifndef TESTFILE
	TESTFILE=
endif

build: dep
	mkdir -p $(JS)
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.map" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-timeago/jquery.timeago.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery.linkify.js" "$(JS)"
	mkdir -p ./deploy/resources/logs/
	touch ./deploy/resources/logs/deity.log
	touch ./deploy/resources/logs/emails.log

dep:
	@$(COMPOSER) install

js-dep:
	npm install

install: build start-chat
	chown www-data:adm ./deploy/resources/logs/emails.log
	chown www-data:adm ./deploy/resources/logs/deity.log
	@echo "Don't forget to update webserver configs as necessary."
	@echo "Including updating the php to retain login sessions longer."
	cp build.properties.tpl build.properties
	cp buildtime.xml.tpl buildtime.xml
	cp connection.xml.tpl connection.xml

install-system:
	@echo "Installing initial system and server dependencies."
	@echo "In the case of the database and webserver,"
	@echo "they need professional admin configuration after initial install."
	@echo "Since we are running php 5.6, you may need to install the fine"
	@echo "php 5.6 ppa by Ondre J to get access to php 5.6, e.g.:"
	@echo "    sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php"
	apt-get install python3 python3-dev python3-lxml unzip
	apt-get install php5.6-cli
	#Installing php5.6 cli 
	apt-get install php5.6-fpm php5.6-xml php5.6-pgsql php5.6-curl php5.6-mbstring
	apt-get install postgresql-client nginx 


start-chat:
	touch /var/log/nginx/ninjawars.chat-server.log
	chown www-data:adm /var/log/nginx/ninjawars.chat-server.log
	nohup php bin/chat-server.php > /var/log/nginx/ninjawars.chat-server.log 2>&1 &


all: build test-unit db python-build test

test-one:
	$(TEST_RUNNER) $(CC_FLAG) $(TESTFILE)

pre-test:
	@find ./deploy/lib/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/www/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/tests/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/cron/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	php deploy/check.php
	# Check for presence of database
	psql -lqt | cut -d \| -f 1 | grep -qw $(DBNAME)

test: pre-test test-main test-functional post-test

test-main:
	@$(TEST_RUNNER) $(CC_FLAG)

test-unit:
	@find "./deploy/lib/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find "./deploy/www/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-quick:
	@find "./deploy/lib/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find "./deploy/www/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@$(TEST_RUNNER) --testsuite Quick
	python3 -m pytest deploy/tests/functional/

test-integration: pre-test
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Integration

test-cron-run:
	@echo "Running all the deity files, aren't you lucky.";
	php ./deploy/cron/tick_atomic.php
	php ./deploy/cron/tick_tiny.php
	php ./deploy/cron/tick_minor.php
	php ./deploy/cron/tick_major.php
	php ./deploy/cron/tick_nightly.php

test-functional:
	python3 -m pytest deploy/tests/functional/

test-js: js-dep
	karma start deploy/tests/karma.conf.js --browsers PhantomJS --single-run

test-ratchets:
	#split out for ci for now
	python3 -m pytest deploy/tests/functional/test_ratchets.py

post-test:
	#noop for now

clean:
	@rm -rf "$(SRC)templates/"compiled/*
	@rm -rf "$(SRC)nginx-1.9.12/"
	@rm -rf "$(SRC)nginx-1.9.12.tar.gz"
	@rm -rf "$(CC_DIR)"
	@rm -f "$(JS)jquery.min.js"
	@rm -f "$(JS)jquery.min.map"
	@rm -f "$(JS)jquery.timeago.js"
	@rm -f "$(JS)jquery.linkify.js"
	@rm -f "$(JS)jquery-linkify.min.js"

dist-clean: clean
	@rm -rf ./vendor/*
	@rm -rf "$(COMPONENTS)"
	@rm -rf "$(SRC)resources/"logs/*
	@rm -rf ./node_modules
	@echo "Done"
	@echo "You'll have to dropdb $(DBNAME) yourself."

db-init:
	# Fail on existing database
	createdb $(DBNAME)
	createuser $(DBUSER)
	psql $(DBNAME) -c "CREATE ROLE $(DBROLE);"
	psql $(DBNAME) -c "GRANT $(DBROLE) to ${DBUSER}"
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO $(DBROLE);"

db:
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO $(DBROLE);"
	psql $(DBNAME) -c "CREATE EXTENSION pgcrypto"
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"
	$(PROPEL)
	$(PROPEL) convert-conf
	$(PROPEL) insert-sql
	$(PROPEL) . migrate
	psql $(DBNAME) < ./deploy/sql/custom_schema_migrations.sql
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"
	psql $(DBNAME) -c "\d" | head -30
	#psql $(DBNAME) -c "REASSIGN OWNED BY ${DBCREATINGUSER} TO $(DBROLE);"
	psql $(DBNAME) -c "\d" | head -30


db-fixtures:
	psql $(DBNAME) < ./deploy/sql/fixtures.sql

migration:
	$(PROPEL)
	$(PROPEL) convert-conf
	$(PROPEL) . diff migrate
	$(PROPEL) . diff migrate
	$(PROPEL) om

web-start:
	#Symlink /tmp/www/ in place of /var/www/
	rm -rf /tmp/root/
	ln -s $(SRC) /tmp/root
	#permission error is normal and recoverable
	${NGINX_PATH} -c `pwd`/deploy/conf/nginx.conf
	sleep 0.5
	ps waux | grep nginx

web-stop:
	${NGINX_PATH} -c `pwd`/deploy/conf/nginx.conf -s stop
	sleep 0.5
	ps waux | grep nginx
	# server may be stopped now

web-reload:
	${NGINX_PATH} -c `pwd`/deploy/conf/nginx.conf -s reload
	sleep 0.5
	ps waux | grep nginx

ci-pre-configure:
	# Set php version through phpenv. 5.3 through 7.0 available
	phpenv local 5.6
	@echo "Removing xdebug on CI, by default."
	rm -f /home/rof/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
	ln -s `pwd` /tmp/root
	#precache composer for ci
	composer config -g github-oauth.github.com $(GITHUB_ACCESS_TOKEN)
	composer install --prefer-dist --no-interaction
	# Set up the resources file, replacing first occurance of strings with their build values
	sed -i "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php
	sed -i "s|/srv/ninjawars/|../..|g" deploy/tests/karma.conf.js
	ln -s resources.build.php deploy/resources.php
	ln -s build.properties.tpl build.properties
	ln -s buildtime.xml.tpl buildtime.xml
	ln -s connection.xml.tpl connection.xml
	# Set up selenium and web server for browser tests
	#wget http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar
	#java -jar selenium-server-standalone-2.42.2.jar > selenium_server_output 2>&1 &
	#Switch from python2 to python3
	rm -rf ${HOME}/.virtualenv
	which python3
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"

python-install:
	# Install python3 deps with pip
	pip3 install virtualenv
	pip3 install -r ./deploy/requirements.txt

ci: ci-pre-configure build python-install test-unit db-init db db-fixtures

ci-test: pre-test test-main test-cron-run test-ratchets test-js post-test
