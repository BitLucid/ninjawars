.PHONY: test test-integration test-unit clean dep build dist-clean

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

build: dep
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.map" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-timeago/jquery.timeago.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery.linkify.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery-linkify.min.js" "$(JS)"

test:
	php deploy/check.php
	@find ./deploy/core/ -iname "*.php" -exec php -l {} \;
	@find ./deploy/www/ -iname "*.php" -exec php -l {} \;
	@$(TEST_RUNNER) $(CC_FLAG)
	python3 -m pytest deploy/tests/functional/test_ratchets.py

test-unit:
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-integration:
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Integration

test-functional:
	python3 -m pytest deploy/tests/functional/

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

db:
	# Fail on existing database
	createdb $(DBNAME)
	psql $(DBNAME) -c "CREATE EXTENSION pgcrypto"
	psql $(DBNAME) < ./deploy/sql/custom_schema_migrations.sql
	vendor/bin/propel-gen
	vendor/bin/propel-gen convert-conf
	vendor/bin/propel-gen insert-sql
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen om


db-fixtures:
	psql $(DBNAME) < ./deploy/sql/fixtures.sql

migration:
	vendor/bin/propel-gen
	vendor/bin/propel-gen convert-conf
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen . diff migrate
	vendor/bin/propel-gen om

ci-install: python-build
	# Set php version through phpenv. 5.3, 5.4 and 5.5 available
	phpenv local 5.5
	#precache composer for ci
	composer install --prefer-source --no-interaction



python-build:
	#Switch from python2 to python3
	rm -rf ${HOME}/.virtualenv
	which python3
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"
	# Install python3 deps with pip
	pip install -r ./deploy/requirements.txt

post-test:
	find ./deploy/cron/ -iname "*.php" -exec php -l {} \;
	find ./deploy/tests/ -iname "*.php" -exec php -l {} \;
