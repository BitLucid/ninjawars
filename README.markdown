# Ninjawars repository

The source code dojo for the [Ninja Game](https://www.ninjawars.net) @ https://ninjawars.net .

[Codeship Continuous Integration build](https://www.codeship.io/projects/41292)

![Codeship Status for BitLucid/ninjawars](https://codeship.com/projects/7c7b3800-3608-0132-36b5-4e1d56e5e814/status)

## Install

Install your webserver (nginx + php7-fpm recommended) & configure it

	sudo apt-get install php7-cli php7-fpm nginx

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
	make db-init-all
	make db-fixtures
	make check

Get the database working, then make install
	sudo make install

To sync up to the latest db changes:

	cd /srv/ninjawars
	sudo bash ./scripts/build/integration.sh

Start up the chat server with this:

	sudo make start-chat

Then you can run the tests to check your progress with:

    make test

*See ./docs/INSTALL if you need more.*

## Deployment Process

Ninjwars is deployed helpfully via Deploybot https://ninjawars.deploybot.com/
Triggered by pull request merges.
Vetted by continuous integration here:
 - https://app.codeship.com/home
It gets deployed currently to the aws servers here: 
 - https://console.aws.amazon.com/ec2/v2/home?region=us-east-1#Instances:instanceState=running
Loadbalanced behind balancers here:
 - https://console.aws.amazon.com/ec2/v2/home?region=us-east-1#LoadBalancers:sort=loadBalancerName
With additional frontends here: 
 - https://www.ninjawars.net
 - https://splash.ninjawars.net
 - https://nwave.ninjawars.net
 - https://shell.ninjawars.net/
 - https://console.aws.amazon.com/cloudfront/home?region=us-east-1

## Run the Docker

* Init tagged image: `docker build -t nw-server .`
* Run it: `docker run --rm -it -p 7654:7654 nw-server`
* Stop the container: `docker stop nw-server`

## To Contribute

See CONTRIBUTING.md
