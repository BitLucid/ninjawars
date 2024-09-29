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
GITHUB_ACCESS_TOKEN=

-include CONFIG

ifdef NOCOVER
	CC_FLAG=
endif

ifndef TESTFILE
	TESTFILE=
endif

build: create-structure dep link-deps check-vendors-installed

# Note that the vendor creation in the below is not the same
# as the RELATIVE_VENDOR env var, which is pathing related
create-structure:
	mkdir -p $(JS)
	mkdir -p deploy/$(VENDOR)
	rm -rf vendor
	ln -s deploy/vendor vendor
	rm -rf ./deploy/templates/compiled/* ./deploy/templates/cache/*
	mkdir -p ./deploy/templates/compiled ./deploy/templates/cache ./deploy/resources/logs/ /tmp/game_logs/
	chmod -R ugo+rwX ./deploy/templates/compiled ./deploy/templates/cache /tmp/game_logs/


link-deps:
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery/jquery.min.map" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-timeago/jquery.timeago.js" "$(JS)"
	@ln -sf "$(RELATIVE_COMPONENTS)jquery-linkify/jquery.linkify.js" "$(JS)"
	@ln -sf "$(RELATIVE_VENDOR)twbs/bootstrap/dist/css/bootstrap.min.css" "$(CSS)"
	@ln -sf "$(RELATIVE_VENDOR)twbs/bootstrap/dist/js/bootstrap.min.js" "$(JS)"


dep: js-deps
	@echo "NW step: dep: composer validate then composer install"
	@$(COMPOSER) validate
	@$(COMPOSER) install


check-vendors-installed:
# Throw error if the vendor directories are not installed
	@ls vendor/ && cd deploy && ls vendor/ && cd ..

refresh-vendor:
	rm -rf vendor
	ln -sf deploy/vendor vendor

check: pre-test

checkbase:
	@echo "If the following fails, check that /vendor and /deploy/vendor are symlinked, and that composer install has run"
	php ./deploy/checkbase.php
	php deploy/resources.php
	php deploy/lib/base.inc.php

js-deps:
	node -v
	corepack enable
	echo "corepack enable DONE. Totally sidesteps having to install a yarn version"
	yarn -v
	corepack enable
	yarn install --immutable

resources-file:
	sed -i "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php
	sed -i "s|/srv/ninjawars/|../..|g" deploy/tests/karma.conf.js
	ln -sf resources.build.php deploy/resources.php

preconfig: composer-ratelimit-check resources-file
	@echo "NW step: Setting up composer github access token to avoid ratelimit."
ifndef COMPOSER
$(error COMPOSER is not set)
endif
ifndef GITHUB_ACCESS_TOKEN
$(error GITHUB_ACCESS_TOKEN is not set)
endif
ifndef COMPOSER_AUTH
$(error COMPOSER_AUTH is not set)
endif
	@$(COMPOSER) --version

postcheck:
	@echo "Don't forget to update webserver configs as necessary."
	@echo "Including updating the php to retain login sessions longer."
	php ./deploy/check.php
	echo "Check that the webserver user has permissions to the script!"

install: preconfig build postcheck

install-admin: preconfig build start-chat writable postcheck


writable:
	mkdir -p ./deploy/templates/compiled ./deploy/templates/cache /tmp/game_logs/ ./deploy/resources/logs/
	chmod -R ugo+rw ./deploy/templates/compiled ./deploy/templates/cache
	chmod -R ugo+rw /tmp/game_logs/ || true


install-system:
	@echo "Installing initial system and server dependencies."
	@echo "In the case of the database and webserver,"
	@echo "they need professional admin configuration after initial install."
	@echo "Since we are running php 8, you may need to install a source repo for php8"
	@echo "For the ubuntu ppa, see: https://launchpad.net/~ondrej/+archive/ubuntu/php "
	apt-get -y install python3 python3-dev python3-venv python3-pip python3-lxml unzip
	# PHP!
	echo "Installing php cli"
	apt-get -y install php8.2-cli
	apt-get -y install php8.2-fpm php8.2-xml php8.2-pgsql php8.2-curl php8.2-mbstring php8.2-intl
	phpenmod xml pgsql curl mbstring
	# Note that xml is what installs the ext-dom
	apt install curl 
	curl https://raw.githubusercontent.com/creationix/nvm/master/install.sh | bash 
	echo "Also run: make install-node"
	echo "Configure your webserver and postgresql yourself, we recommend nginx ^1.14.0 and postresql ^10.0"
	echo "If you want ssl with the nginx site, use: https://www.digitalocean.com/community/tutorials/how-to-create-a-self-signed-ssl-certificate-for-nginx-in-ubuntu-16-04"

install-node:
	corepack enable
	nvm install

install-python: python-install

install-webserver:
	apt install nginx

install-database-client:
	apt install postgresql-client

start-chat:
	touch /tmp/game_logs/ninjawars.chat-server.log
	chmod ugo+rw /tmp/game_logs/ninjawars.chat-server.log
	nohup php bin/chat-server.php > /tmp/game_logs/ninjawars.chat-server.log 2>&1 &

browse:
	xdg-open https://localhost:8765

browse-repo:
	xdg-open https://github.com/BitLucid/ninjawars/pulls

all: build test-unit db python-build test

test-one:
	./vendor/bin/phpunit-watcher watch $(TESTFILE)

test-one-no-watch:
	php ./vendor/bin/phpunit $(TESTFILE)


create-artifact:
	echo "Creating an artifact for future deployment"
	echo "Make sure to make dep to build deployable composer and node assets before this"
	echo "Currently node_modules is not directly included in the artifact, as we just use it for tests for now"
	mkdir -p ./deploy/artifacts
	tar -czv -X artifacts-exclude-list.txt -f ./deploy/artifacts/ninjawars-`date +\%F-hour-\%H-min-\%M-sec-\%S-milisec-\%3N`.tar.gz ./composer.json ./composer.lock ./.nvmrc ./.phpver ./.yarnrc.yml ./package.json ./yarn.lock ./Makefile ./deploy/
	echo "Artifact created, see ./deploy/artifacts/ for the latest,"
	echo "Note the high importance of overwriting the resources.php config file in the final"

clean-artifacts:
	rm -rf ./deploy/artifacts/*
	rm -rf ./nw-artifact

send-artifacts:
	aws s3 sync  --include '*.tar.gz' ./deploy/artifacts/ s3://ninjawars-deployment-artifacts/

expand-local-artifact:
	echo "unzipping the latest tar to the local nw-artifact directory"
	mkdir -p ./nw-artifact
	tar -xzvf ./deploy/artifacts/*.tar.gz -C ./nw-artifact
	mkdir -p ./nw-artifact/deploy/templates/compiled ./nw-artifact/deploy/templates/cache /tmp/game_logs/ ./nw-artifact/deploy/resources/logs/
	chmod -R ugo+rw ./nw-artifact/deploy/templates/compiled ./nw-artifact/deploy/templates/cache
	chmod -R ugo+rw /tmp/game_logs/ || true
	ls ./nw-artifact

browse-artifact:
	xdg-open https://nw-artifact.local
	

watch:
	./vendor/bin/phpunit-watcher watch

pre-test:
	@echo "To test one test use ./vendor/bin/phpunit --filter methodName deploy/tests/something/file.php"
	@find ./deploy/lib/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/www/ -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/tests/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find ./deploy/cron/ -iname "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	# Check for presence of database
	psql -lqt | cut -d \| -f 1 | grep -w $(DBNAME)
	php deploy/check.php
	@echo "To test one test use ./vendor/bin/phpunit --filter methodName deploy/tests/something/file.php"
	@$(COMPOSER) validate

test: pre-test test-main test-functional test-js post-test

test-main:
	@$(TEST_RUNNER) $(CC_FLAG)

check-for-syntax-errors:
	@find "./deploy/lib/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true
	@find "./deploy/www/" -name "*.php" -exec php -l {} \;|grep -v "No syntax errors" || true

test-unit: check-for-syntax-errors
	@$(TEST_RUNNER) $(CC_FLAG) --testsuite Unit

test-quick: check-for-syntax-errors
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
	@echo "See an example spec file in ./deploy/tests/js/ChatSpec.js"
	@echo "Most of the files being tested are in ./deploy/www/js/ e.g. ./deploy/www/js/chat.js"
	yarn test

test-ratchets:
	#split out for ci for now
	python3 -m pytest deploy/tests/functional/test_ratchets.py

test-cleanup:
	# Sometimes partial test runs create problem ninja accounts, this is how to clean all that up
	psql nw -c "delete from accounts where account_id in (select account_id from accounts join account_players ap on accounts.account_id = ap._account_id join players on ap._player_id = players.player_id where uname like 'phpunit_%')"
	psql nw -c "delete from players where uname like 'phpunit_%'"

post-test:
	#noop for now

clean:
	@rm -rf "$(SRC)templates/"compiled/*
	@rm -rf "$(CC_DIR)"
	@rm -f "$(JS)jquery.min.js"
	@rm -f "$(JS)jquery.min.map"
	@rm -f "$(JS)jquery.timeago.js"
	@rm -f "$(JS)jquery.linkify.js"
	@rm -f "$(JS)jquery-linkify.min.js"
	@rm -f "/tmp/nw"
	@rm -rf ./deploy/templates/cache/* && touch ./deploy/templates/cache/.gitkeep
	@rm -rf ./deploy/templates/compiled/* ./deploy/resources/logs/deity.log ./deploy/resources/logs/emails.log
	@rm -rf ./deploy/www/index.html ./deploy/www/intro.html ./deploy/www/login.html ./deploy/www/signup.html
	@echo "Cleaned up, you will want to re: make build to get the js files back"

dist-clean: clean
	@rm -rf "$(VENDOR)"*
	@rm -rf "$(COMPONENTS)"
	@rm -rf "$(SRC)resources/"logs/*
	@rm -rf ./node_modules
	@echo "Done"
	@echo "You'll have to dropdb $(DBNAME) yourself."


clear-vendor:
	rm -rf vendor deploy/vendor
	@echo "vendor and deploy/vendor cleared, you will want to re: make create-structure to get the directories back"

	


clear-cache:
	php ./deploy/lib/control/util/clear_cache.php

db-init-all: db-init db-init-roles db-init-grants db

db-init:
	# Fail on existing database
	createdb $(DBNAME);
	# Or CREATE DATABASE "nw_someTHING" WITH OWNER "postgres" ENCODING 'UTF8' TABLESPACE "pg_default";
	createuser $(DBUSER);

db-init-roles:
	# Set up the roles as needed, errors if pre-existing, so split out
	psql $(DBNAME) -c "CREATE ROLE $(DBROLE);"

db-init-grants:
	psql $(DBNAME) -c "GRANT $(DBROLE) to ${DBUSER}"
	psql $(DBNAME) -c "GRANT ALL PRIVILEGES ON DATABASE ${DBNAME} TO $(DBROLE);"
	psql $(DBNAME) -c "REASSIGN OWNED BY ${DBUSER} TO $(DBROLE);"
	# e.g. reassign owned by coco to developers;

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
	#permission error is normal and recoverable
	service nginx reload
	sleep 0.5
	ps waux | grep nginx

web-stop:
	service nginx reload
	sleep 0.5
	ps waux | grep nginx
	# server may be stopped now

web-reload:
	service nginx reload
	sleep 0.5
	ps waux | grep nginx

restart-webserver:
	service nginx reload
	sleep 0.5
	ps waux | grep nginx

post-deploy-restart: # for deploybot after deployment
	sh /srv/ninjawars/deploy/cron/queued_restart.sh

restart-post-deploy: post-deploy-restart

post-deploy-queue-restart:
	touch /tmp/queued_restart.pid
	touch /tmp/last_queue_restart_date

post-deploy: post-deploy-queue-restart deployment-final-email
	echo "Email sent, post deployment complete"

link-vendor:
	rm -rf ./vendor
	ln -sf ./deploy/vendor ./vendor

ci-pre-configure: composer-ratelimit-check resources-file
	# Set php version
	sem-version php 8.0
	#@echo "Removing xdebug on CI, by default."
	#rm -f /home/rof/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
	#ln -s `pwd` /tmp/root
	#precache composer for ci
	@echo "Github access token set by environment var COMPOSER_AUTH"
	@$(COMPOSER) install --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader
	# Set up the resources file, replacing first occurance of strings with their build values
	#Switch from python2 to python3
	which python3
	rm -rf ${HOME}/.virtualenv
	which python3
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"

deployment-post-upload: composer-ratelimit-check
	chmod u+x ./composer.phar
	php -v && nvm -v && nvm install && nvm use && node -v # Reflects the .nvmrc file
	corepack enable # allows simple, reliable yarn usage
	echo "Github access token set by environment var COMPOSER_AUTH"
	./composer.phar install --prefer-dist --no-interaction --optimize-autoloader

composer-ratelimit-setup:
	@echo "Exporting a COMPOSER_AUTH env var"
	@export COMPOSER_AUTH=$(COMPOSER_AUTH)

deployment-final-check: check-vendors-installed

deployment-final-email:
	php ./deploy/deployed_scripts/deployment_email.php

composer-ratelimit-check:
	@echo "Will error if composer_AUTH not available to use"
ifndef COMPOSER_AUTH
$(error COMPOSER_AUTH is not set)
endif




python-install:
	which python3
	# Install python3 deps with pip
	python3 -m pip install virtualenv
	#python3 -m venv .venv
	virtualenv -p /usr/bin/python3 "${HOME}/.virtualenv"
	. ~/.virtualenv/bin/activate
	python3 -m pip install -r ./deploy/requirements.txt

ci: ci-pre-configure build python-install test-unit db-init db-init-roles db-init-grants db db-fixtures

ci-test: pre-test test-cron-run test-main test-cron-run test-ratchets post-test
