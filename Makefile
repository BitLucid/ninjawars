.PHONY: all ci pre-test test test-main test-integration test-unit test-quick test-functional test-js post-test clean dep build install install-system dist-clean db db-fixtures

DOMAIN=https://nw.local/
VENDOR=./vendor/
COMPOSER=./composer.phar
CC_DIR=./cc
CC_FLAG=--coverage-html $(CC_DIR)
TEST_RUNNER=php -d zend_extension=xdebug.so $(VENDOR)bin/phpunit
RELATIVE_COMPONENTS=../components/
RELATIVE_VENDOR=../../vendor/
SRC:=`pwd`/deploy/
WWW=$(SRC)www/
COMPONENTS=$(WWW)components/
JS=$(WWW)js/
CSS=$(WWW)css/
DBROLE=developers
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
	@ln -sf "$(RELATIVE_VENDOR)twbs/bootstrap/dist/css/bootstrap.min.css" "$(CSS)"
	@ln -sf "$(RELATIVE_VENDOR)twbs/bootstrap/dist/js/bootstrap.min.js" "$(JS)"
	rm -rf ./deploy/templates/compiled/* ./deploy/templates/cache/*
	mkdir -p ./deploy/templates/compiled ./deploy/templates/cache ./deploy/resources/logs/
	chmod -R ugo+rwX ./deploy/templates/compiled ./deploy/templates/cache
	touch ./deploy/resources/logs/deity.log
	touch ./deploy/resources/logs/emails.log

dep:
	@$(COMPOSER) install

check: pre-test

js-deps:
	yarn install

install: build start-chat writable
	@echo "Don't forget to update webserver configs as necessary."
	@echo "Including updating the php to retain login sessions longer."
	cp -u -p ./deploy/resources.build.php ./deploy/resources.php
	echo "Note that this does not overwrite existing resources.php"
	php ./deploy/check.php
	echo "Check that the webserver user has permissions to the script!"

writable:
	chown ${WEBUSER} ./deploy/resources/logs/*
	mkdir -p ./deploy/templates/compiled ./deploy/templates/cache ./deploy/resources/logs/
	chown ${WEBUSER} ./deploy/resources/logs/*
	chmod -R ugo+rw ./deploy/templates/compiled ./deploy/templates/cache ./deploy/resources/logs/*


install-system:
	@echo "Installing initial system and server dependencies."
	@echo "In the case of the database and webserver,"
	@echo "they need professional admin configuration after initial install."
	@echo "Since we are running php 7, you may need to install a source repo for php7"
	@echo "For the ubuntu ppa, see: https://launchpad.net/~ondrej/+archive/ubuntu/php "
	apt-get install python3 python3-dev python3-pip python3-lxml unzip
	# PHP!
	echo "Installing php cli"
	apt-get install php7.2-cli
	apt-get install php7.2-fpm php7.2-xml php7.2-pgsql php7.2-curl php7.2-mbstring
	phpenmod xml pgsql curl mbstring
	echo "Configure your webserver and postgresql yourself, we recommend nginx ^1.14.0 and postresql ^10.0"
	echo "If you want ssl with the nginx site, use: https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu-16-04"

install-python:
	python3 -m venv .venv
	. .venv/bin/activate
	pip3 install -r requirements.txt

install-webserver:
	apt install nginx

install-database-client:
	apt install postgresql-client

start-chat:
	touch ./deploy/resources/logs/ninjawars.chat-server.log
	chown ${WEBUSER} ./deploy/resources/logs/ninjawars.chat-server.log
	nohup php bin/chat-server.php > ./deploy/resources/logs/ninjawars.chat-server.log 2>&1 &

browse:
	xdg-open https://localhost:8765


all: build test-unit db python-build test

test-one:
	$(TEST_RUNNER) $(CC_FLAG) $(TESTFILE)

watch:
	./vendor/bin/phpunit-watcher watch

pre-test:
	@find ./deploy/lib/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/www/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/tests/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/cron/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	php deploy/check.php
	# Check for presence of database
	psql -lqt | cut -d \| -f 1 | grep -w $(DBNAME)

test: pre-test test-main test-functional test-js post-test

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

test-js:
	yarn test

test-ratchets:
	#split out for ci for now
	python3 -m pytest deploy/tests/functional/test_ratchets.py

test-cleanup:
	psql nw -c "delete from accounts where account_id in (select account_id from accounts join account_players ap on accounts.account_id = ap._account_id join players on ap._player_id = players.player_id where uname like 'phpunit_%')"
	psql nw -c "delete from players where uname like 'phpunit_%'"

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
	@rm -f "/tmp/nw"

dist-clean: clean
	@rm -rf "$(VENDOR)"*
	@rm -rf "$(COMPONENTS)"
	@rm -rf "$(SRC)resources/"logs/*
	@rm -rf ./node_modules
	@echo "Done"
	@echo "You'll have to dropdb $(DBNAME) yourself."

clear-cache:
	php ./deploy/lib/control/util/clear_cache.php

db-init-all: db-init db-init-roles db-init-grants db

db-init:
	# Fail on existing database
	createdb $(DBNAME);
	createuser $(DBUSER);

db-init-roles:
	# Set up the roles as needed, errors if pre-existing, so split out
	psql $(DBNAME) -c "CREATE ROLE $(DBROLE);"

db-init-grants:
	psql $(DBNAME) -c "GRANT $(DBROLE) to ${DBUSER}"
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO $(DBROLE);"
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"

db:
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO $(DBROLE);"
	psql $(DBNAME) -c "CREATE EXTENSION IF NOT EXISTS pgcrypto"
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"
	psql $(DBNAME) < ./deploy/sql/schema.sql
	psql $(DBNAME) < ./deploy/sql/custom_schema_migrations.sql
	cat ./deploy/sql/migrations/* | psql $(DBNAME)
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"
	psql $(DBNAME) -c "\d" | head -30
	#psql $(DBNAME) -c "REASSIGN OWNED BY ${DBCREATINGUSER} TO $(DBROLE);"
	psql $(DBNAME) -c "\d" | grep "player"


db-fixtures:
	psql $(DBNAME) < ./deploy/sql/fixtures.sql

backup-live-db:
	pg_dump -h nw-live-pg10.ci1h1yzrwhkt.us-east-1.rds.amazonaws.com -p 5987 -U ninjamaster nw_live > /srv/backups/nw/nw_live_$(date +\%F-hour-\%H).sql

web-start:
	rm /tmp/root
	ln -s "$(SRC)../" /tmp/root
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
	# Set php version
	# Versions available: https://documentation.codeship.com/basic/languages-frameworks/php/#versions-and-setup
	phpenv local 7.2
	@echo "Removing xdebug on CI, by default."
	rm -f /home/rof/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
	ln -s `pwd` /tmp/root
	#precache composer for ci
	@$(COMPOSER) config -g github-oauth.github.com $(GITHUB_ACCESS_TOKEN)
	@$(COMPOSER) install --prefer-dist --no-interaction
	# Set up the resources file, replacing first occurance of strings with their build values
	sed -i "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php
	sed -i "s|/srv/ninjawars/|../..|g" deploy/tests/karma.conf.js
	ln -s resources.build.php deploy/resources.php
	#Switch from python2 to python3
	rm -rf ${HOME}/.virtualenv
	which python3
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"

python-install:
	# Install python3 deps with pip
	python3 -m pip install virtualenv
	python3 -m pip install -r ./deploy/requirements.txt

ci: ci-pre-configure build python-install test-unit db-init db-init-roles db-init-grants db db-fixtures

ci-test: pre-test test-main test-cron-run test-ratchets post-test
