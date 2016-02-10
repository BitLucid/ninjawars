.PHONY: all ci pre-test test test-integration test-unit test-functional post-test clean dep build dist-clean db db-fixtures migration

COMPOSER=./composer.phar
CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)
TEST_RUNNER=php -d zend_extension=xdebug.so ./vendor/bin/phpunit
RELATIVE_COMPONENTS=../components/
SRC=./deploy/
WWW=$(SRC)www/
COMPONENTS=$(WWW)components/
JS=$(WWW)js/

-include CONFIG

ifdef NOCOVER
	CC_FLAG=
endif

all: build test-unit db python-build test test-functional

build: dep
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.map" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-timeago/jquery.timeago.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery.linkify.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery-linkify.min.js" "$(JS)"


pre-test:
	php deploy/check.php
	# Check for presence of database
	psql -lqt | cut -d \| -f 1 | grep -qw $(DBNAME)

test:
	@find ./deploy/core/ -name "*.php" -exec php -l {} \;
	@find ./deploy/www/ -name "*.php" -exec php -l {} \;
	@$(TEST_RUNNER) $(CC_FLAG)
	python3 -m pytest deploy/tests/functional/test_ratchets.py

test-unit:
	@find "./deploy/core/" -name "*.php" -exec php -l {} \;
	@find "./deploy/www/" -name "*.php" -exec php -l {} \;
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-integration: pre-test
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Integration

test-functional:
	python3 -m pytest deploy/tests/functional/

post-test:
	find ./deploy/cron/ -iname "*.php" -exec php -l {} \;
	@echo "Running all the deity files, aren't you lucky.";
	php deploy/cron/*.php
	find ./deploy/tests/ -iname "*.php" -exec php -l {} \;

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
	@echo "You'll have to dropdb nw yourself."	

db:
	# Fail on existing database
	createdb $(DBNAME)
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO developers;"
	psql $(DBNAME) -c "CREATE EXTENSION pgcrypto"
	#psql $(DBNAME) < ./deploy/sql/custom_schema_migrations.sql
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBCREATINGUSER} TO developers;"
	psql $(DBNAME) -c "\d"
	vendor/bin/propel-gen
	vendor/bin/propel-gen convert-conf
	vendor/bin/propel-gen insert-sql
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen om
	psql $(DBNAME) < ./deploy/sql/custom_schema_migrations.sql
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBCREATINGUSER} TO developers;"
	psql $(DBNAME) -c "\d" | head -30


db-fixtures:
	psql $(DBNAME) < ./deploy/sql/fixtures.sql

migration:
	vendor/bin/propel-gen
	vendor/bin/propel-gen convert-conf
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen om

ci-pre-configure:
	# Set php version through phpenv. 5.3, 5.4 and 5.5 available
	phpenv local 5.5
	#precache composer for ci
	composer install --prefer-source --no-interaction
	# Set up the resources file, replacing first occurance of strings with their build values
	sed -i "0,/postgres/{s/postgres/${PG_USER}/}" deploy/resources.build.php
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
	pip install -r ./deploy/requirements.txt

ci: ci-pre-configure python-install test-unit db db-fixtures

ci-test: pre-test test post-test