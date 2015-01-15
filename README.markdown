# Ninjawars repository [![Build Status](https://travis-ci.org/BitLucid/ninjawars.png?branch=news)](https://travis-ci.org/BitLucid/ninjawars) [ ![Codeship Status for BitLucid/ninjawars](https://www.codeship.io/projects/7c7b3800-3608-0132-36b5-4e1d56e5e814/status)](https://www.codeship.io/projects/41292)
The source code dojo for the [Ninja Game](http://www.ninjawars.net) @ http://ninjawars.net .

## Install

Clone this repository

	git clone git@github.com:BitLucid/ninjawars.git

Install system dependencies

	cd /srv/ninjawars
	sudo bash /srv/ninjawars/scripts/build/install.sh
	
Update an out-of-date but already installed instance's system/composer libraries:

	cd /srv/ninjawars
	sudo bash /srv/ninjawars/scripts/build/integration.sh

Check the differences between your database and the latest schema:

    vendor/bin/propel-gen diff

Sync the database to make your version get updated with the latest table:

	cd /srv/ninjawars
	./scripts/sync

Install the test environment with:

	cd /srv/ninjawars
	sudo bash /srv/ninjawars/scripts/build/test.sh

Then you can run the tests at any point with:

    ./vendor/bin/phpunit

## Contributing
You can make web commits on github.com, just search github for "ninjawars".  

To contribute
==============

### For simple contribution/collaboration:

- Find the file or code that you want to suggest a fix for, and make a comment with the fixed code, or just the general process to achieve the fix.

### For more in-depth contribution/collaboration:

- Make an account on github & log in.

- Click the button on the ninjawars repository to create your own "fork" of the ninjawars code.
- Find any files you want to change and click the "edit" link to edit your version directly.
- When you're ready, send me a message or hit the "pull request" button on this ninjawars repository to request that your changes get pulled back in to the main ninjawars code.

### For non-web-based contribution:
Download the ninjawars source code, change files, and send the changed text to us, <ninjawarslivebythesword@gmail.com>, we'll try to incorporate the changes on our side appropriately.

### Full Contribution:
Learning how to use the git app on your local machine is highly recommended for any programmer or webdesigner, though there's a high initial learning curve.  My recommendation, especially if you're running windows, is to try setting up git-tortoise [http://code.google.com/p/tortoisegit/](http://code.google.com/p/tortoisegit/) as a really simple solution for starting to harness git's power.  Of course, you can also always just use github's web interface to contribute patches as well.

## Resources

For licensing information (Creative Commons License) read the `ninjawars/deploy/www/staff.php html` file or browse to `/staff.php`.

Talk about development on the forum at: 
[The ninjawars development forum](http://ninjawars.proboards.com/index.cgi?board=Devel1)

Ninjawars code breakdown on ohloh:
[https://www.ohloh.net/p/ninjawars](https://www.ohloh.net/p/ninjawars)

A git cheatsheet:
[http://git.or.cz/gitwiki/GitCheatSheet](http://git.or.cz/gitwiki/GitCheatSheet)

A guide to using git on windows:
[http://nathanj.github.com/gitguide/](http://nathanj.github.com/gitguide/)