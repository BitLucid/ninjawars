# Ninjawars repository

The source code dojo for the [Ninja Game](http://www.ninjawars.net) @ http://ninjawars.net .



[Ninjawars Build Automation](https://tchalvak.semaphoreci.com/projects/ninjawars)

[![Semaphore Build Status](https://tchalvak.semaphoreci.com/badges/ninjawars/branches/master.svg?style=shields&key=7763fdd3-c6ae-47b0-8bef-2f284b53ce10)](https://tchalvak.semaphoreci.com/projects/ninjawars)
[![Deployment status from DeployBot](https://ninjawars.deploybot.com/badge/02267417970828/83400.svg)](https://deploybot.com)

## Install

Install your webserver (nginx + php8.2-fpm recommended) & configure it

    sudo apt-get install php8.2-cli php8.2-fpm nginx

On your database server, install postgresql & configure it

    sudo apt-get install postgresql postgresql-contrib

Set up the environment variables, get the github token
from here: https://github.com/settings/tokens

    export GITHUB_ACCESS_TOKEN=
    export DBUSER=EGkzqai
    sed "0,/postgres/{s/postgres/${DBUSER}/}" deploy/resources.build.php > deploy/resources.php
    sed "s|/srv/ninjawars/|../..|g" deploy/tests/karma.conf.js > karma.conf.js

Local Prep to install php-cli or similar and needed php extensions:

    sudo make install-system

configure, make, make install:

    ./configure
    # edit your generated CONFIG file here
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

_See ./docs/INSTALL if you need more._

## Deployment Process

Ninjwars is deployed helpfully via Deploybot https://ninjawars.deploybot.com/
Triggered by pull request merges.

-   Vetted by continuous integration here:
-   https://tchalvak.semaphoreci.com/projects/ninjawars
-   It gets deployed currently to the aws servers here:
-   https://console.aws.amazon.com/ec2/v2/home?region=us-east-1#Instances:instanceState=running
-   Loadbalanced behind balancers here:
-   https://console.aws.amazon.com/ec2/v2/home?region=us-east-1#LoadBalancers:sort=loadBalancerName
-   With additional frontends here:
-   http://www.ninjawars.net
-   https://splash.ninjawars.net
-   https://nwave.ninjawars.net
-   https://shell.ninjawars.net/
-   https://console.aws.amazon.com/cloudfront/home?region=us-east-1

## Run the Docker

-   Init tagged image: `docker build -t nw-server .`
-   Run it: `docker run --rm -it -p 7654:7654 nw-server`
-   Stop the container: `docker stop nw-server`

## To Contribute

See CONTRIBUTING.md
