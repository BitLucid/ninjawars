.PHONY: all ci pre-test test test-integration test-unit test-functional test-cron post-test clean dep build install dist-clean db db-fixtures migration

COMPOSER=./composer.phar
CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)
TEST_RUNNER=php -d zend_extension=xdebug.so ./vendor/bin/phpunit
RELATIVE_COMPONENTS=../components/
SRC=./deploy/
WWW=$(SRC)www/
COMPONENTS=$(WWW)components/
JS=$(WWW)js/
DBROLE=developers
PROPEL=./vendor/bin/propel-gen

-include CONFIG

ifdef NOCOVER
	CC_FLAG=
endif

ifndef TESTFILE
	TESTFILE=
endif

build: dep
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.map" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-timeago/jquery.timeago.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery.linkify.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery-linkify.min.js" "$(JS)"

install: build
	apt-get install python3-dev python3-lxml
	touch ./deploy/resources/logs/emails.log
	chown www-data:adm ./deploy/resources/logs/emails.log
	touch /var/log/nginx/ninjawars.chat-server.log
	chown www-data:adm /var/log/nginx/ninjawars.chat-server.log
	nohup php bin/chat-server.php > /var/log/nginx/ninjawars.chat-server.log 2>&1 &
	@echo "Don't forget to update webserver configs as necessary."


all: build test-unit db python-build test test-functional

test-one:
	$(TEST_RUNNER) $(CC_FLAG) $(TESTFILE)

pre-test:
	php deploy/check.php
	# Check for presence of database
	psql -lqt | cut -d \| -f 1 | grep -qw $(DBNAME)

test:
	@find ./deploy/lib/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/www/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@$(TEST_RUNNER) $(CC_FLAG)
	python3 -m pytest deploy/tests/functional/test_ratchets.py

test-unit:
	@find "./deploy/lib/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find "./deploy/www/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-integration: pre-test
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Integration

test-cron:
	php ./deploy/cron/deity_fiveminute.php
	php ./deploy/cron/deity_halfhour.php
	php ./deploy/cron/deity_hourly.php
	php ./deploy/cron/deity_nightly.php

test-functional:
	python3 -m pytest deploy/tests/functional/

post-test:
	find ./deploy/cron/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@echo "Running all the deity files, aren't you lucky.";
	php deploy/cron/*.php
	find ./deploy/tests/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true

clean:
	@rm -rf "$(SRC)templates/"compiled/*
	@rm -rf "$(CC_DIR)"
	@rm -f "$(JS)jquery.min.js"
	@rm -f "$(JS)jquery.min.map"
	@rm -f "$(JS)jquery.timeago.js"
	@rm -f "$(JS)jquery.linkify.js"
	@rm -f "$(JS)jquery-linkify.min.js"

dep:
	@$(COMPOSER) install

dist-clean: clean
	@rm -rf ./vendor/*
	@rm -rf "$(COMPONENTS)"
	@rm -rf "$(SRC)resources/"logs/*
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
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBCREATINGUSER} TO $(DBROLE);"
	psql $(DBNAME) -c "\d" | head -30


db-fixtures:
	psql $(DBNAME) < ./deploy/sql/fixtures.sql

migration:
	$(PROPEL)
	$(PROPEL) convert-conf
	$(PROPEL) . diff migrate
	$(PROPEL) . diff migrate
	$(PROPEL) om

ci-pre-configure:
	# Set php version through phpenv. 5.3, 5.4 and 5.5 available
	phpenv local 5.5
	#precache composer for ci
	composer config -g github-oauth.github.com $(GITHUB_ACCESS_TOKEN)
	composer install --prefer-dist --no-interaction
	# Set up the resources file, replacing first occurance of strings with their build values
	sed -i "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php
	#eventually that sed should be made to match only the first hit
	ln -s resources.build.php deploy/resources.php
	# Set up selenium and web server for browser tests
	#wget http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar
	#java -jar selenium-server-standalone-2.42.2.jar > selenium_server_output 2>&1 &
	ln -s build.properties.tpl build.properties
	ln -s buildtime.xml.tpl buildtime.xml
	ln -s connection.xml.tpl connection.xml
	#Switch from python2 to python3
	rm -rf ${HOME}/.virtualenv
	which python3
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"

python-install:
	# Install python3 deps with pip
	pip3 install virtualenv
	pip3 install -r ./deploy/requirements.txt

ci: ci-pre-configure build python-install test-unit db-init db db-fixtures

ci-test: pre-test test post-test test-cron
