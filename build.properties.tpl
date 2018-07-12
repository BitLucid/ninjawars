propel.project = ninjawars

# The Propel driver to use for generating SQL, etc.
propel.database = pgsql

# PDO DSN
propel.database.url = pgsql:dbname=nw;
propel.database.user = ninjamaster

# Environment
propel.namespace.autoPackage = true
propel.runtime.conf.file = connection.xml
propel.buildtime.conf.file = buildtime.xml
propel.output.dir = ${propel.project.dir}/deploy
propel.php.dir = ${propel.output.dir}
propel.phpconf.dir = ${propel.output.dir}/conf
propel.runtime.phpconf.file = connection.php
propel.runtime.phpconf-classmap.file = classmap.php

# Migration setting
propel.migration.table = propel_migration
propel.migration.caseInsensitive = true
propel.migration.dir = ${propel.output.dir}/db/migrations