# Ninjawars repository

The source code dojo for the [Ninja Game](http://www.ninjawars.net) @ http://ninjawars.net .

[Codeship Continuous Integration build](https://www.codeship.io/projects/41292)

![Codeship Status for BitLucid/ninjawars](https://codeship.com/projects/7c7b3800-3608-0132-36b5-4e1d56e5e814/status)

## Install

Install your webserver (nginx + php5-fpm recommended) & configure it

	sudo apt-get install php5-cli php5-fpm nginx

On your database server, install postgresql & configure it

	sudo apt-get install postgresql postgresql-contrib

Set up the environment variables, get the github token 
from here: https://github.com/settings/tokens

	export GITHUB_ACCESS_TOKEN=
	export DBUSER=
	sed "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php > deploy/resources.php
	sed "s|/srv/ninjawars/|../..|g" deploy/tests/karma.conf.js > karma.conf.js

configure, make, make install:

	./configure
	make
	sudo make install

	
Sync up to the latest db changes:

	cd /srv/ninjawars
	sudo bash ./scripts/build/integration.sh

Check the differences between your database and the latest schema:

    vendor/bin/propel-gen diff

Sync the database to make your version get updated with the latest table:

	cd /srv/ninjawars
	./scripts/sync

Start up the chat server with this:

	sudo make start-chat

Then you can run the tests to check your progress with:

    make test

*See ./docs/INSTALL if you need more.*

## Run the Docker

* Init tagged image: `docker build -t nw-server .`
* Run it: `docker run --rm -it -p 7654:7654 nw-server`
* Stop the container: `docker stop nw-server`

## To Contribute

See CONTRIBUTING.md
