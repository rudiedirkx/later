<?php

use rdx\later\ExactMatch;

require 'vendor/autoload.php';
require 'env.php';

header('Content-type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

session_set_cookie_params(['httponly' => true, 'secure' => true, 'lifetime' => 99999999, 'samesite' => 'None']);
session_start();

// db connection
$db = db_sqlite::open(array('database' => __DIR__ . '/db/later.sqlite3'));
if ( !$db ) {
	exit('No database connecto...');
}

$db->ensureSchema(require 'inc.db-schema.php');

define('SESSION_NAME', 'later');
define('DT', 'd M H:i');

$fgcContext = stream_context_create(array(
	'http' => array(
		'user_agent' => 'Later 1.0',
	),
));

$_initClass = function($config) {
	$params = (array) $config;
	$class = array_shift($params);
	return new $class(...$params);
};
$g_bookmarkMatchers = array_map($_initClass, LATER_BOOKMARK_MATCHERS ?: [ExactMatch::class]);
$g_bookmarkPreprocessors = array_map($_initClass, LATER_BOOKMARK_PREPROCESSORS);
