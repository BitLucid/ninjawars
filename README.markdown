# Ninjawars repository [ ![Codeship Status for BitLucid/ninjawars](https://www.codeship.io/projects/7c7b3800-3608-0132-36b5-4e1d56e5e814/status)](https://www.codeship.io/projects/41292)
The source code dojo for the [Ninja Game](http://www.ninjawars.net) @ http://ninjawars.net .

## Install

Install system dependencies

	cd /srv/ninjawars
	composer install
	sudo bash ./scripts/build/install.sh
	ln -s .gitmodules.tpl .gitmodules
	git submodule update --init --recursive
	
Sync up to the latest changes:

	cd /srv/ninjawars
	sudo bash ./scripts/build/integration.sh

Check the differences between your database and the latest schema:

    vendor/bin/propel-gen diff

Sync the database to make your version get updated with the latest table:

	cd /srv/ninjawars
	./scripts/sync

Install the test environment with:

	cd /srv/ninjawars
	sudo bash ./scripts/build/test.sh

Start up the chat server with your customized version of these commands:

	sudo touch /var/log/nginx/ninjawars.chat-server.log
	sudo chown kzqai:dev /var/log/nginx/ninjawars.chat-server.log
	cd /srv/ninjawars/
	nohup php bin/chat-server.php > /var/log/nginx/ninjawars.chat-server.log 2>&1 &
	(rather hack-ey and new for now)

Then you can run the tests to check your progress with:

    ./vendor/bin/phpunit

*See ./docs/INSTALL if you need more.*

## To Contribute

See CONTRIBUTING.md