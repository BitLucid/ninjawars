<?php
require_once(ROOT.'resources.php');
require_once(ROOT.'vendor/autoload.php');

/**
 * Start up the illuminate database connection, using the database connection info in resources.php
 */

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$info = [
    'driver'    => 'pgsql',
    'database'  => DATABASE_NAME,
    'username'  => DATABASE_USER,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => ''
];

if (DATABASE_USE_HOST) {
	$info['host'] = DATABASE_HOST;
}

if (DATABASE_USE_PASSWORD) {
	$info['password'] = DATABASE_PASSWORD;

}
if (DATABASE_USE_PORT) {
	$info['port'] = DATABASE_PORT;
}

$capsule->addConnection($info);

$capsule->bootEloquent();
