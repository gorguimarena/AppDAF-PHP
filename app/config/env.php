<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('../');

$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_DRIVE', $_ENV['DB_DRIVE']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);
define('METHODE_INSTANCE_NAME', $_ENV['METHODE_INSTANCE_NAME']);
define('SERVICES_PATH', $_ENV['SERVICES_PATH']);

