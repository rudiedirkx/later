<?php

require 'env.php';
require 'inc.functions.php';

header('Content-type: text/html; charset=utf-8');

isset($_COOKIE[session_name()]) and session_start();

require WHERE_DB_GENERIC_AT . '/db_sqlite.php';

// db connection
$db = db_sqlite::open(array('database' => __DIR__ . '/db/later.sqlite3'));
if ( !$db ) {
	exit('No database connecto...');
}

$schema = require 'inc.db-schema.php';
require 'inc.ensure-db-schema.php';

define('SESSION_NAME', 'later');
define('DT', 'd M H:i');

$fgcContext = stream_context_create(array(
	'http' => array(
		'user_agent' => 'Later 1.0',
	),
));
