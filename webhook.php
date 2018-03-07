<?php

/**
 * Input format:
 *
 * >
 * > Title (optional)
 * >
 * > URL
 * >
 */

require 'inc.bootstrap.php';

header('Content-type: text/plain');

if ( !is_logged_in(false) ) {
	header('HTTP/1.1 401 Unauthorized');
	exit("You must be logged in");
}

$body = trim(@$_REQUEST['title'] . "\n" . @$_REQUEST['notes']);

if ( preg_match('#https?://\S+#', $body, $match, PREG_OFFSET_CAPTURE) ) {
	$url = $match[0][0];
	$title = trim(substr($body, 0, $match[0][1]));

	$saved = do_save($url, $title);
	if ( $saved ) {
		exit('OK');
	}

	header('HTTP/1.1 500 Error');
	exit("Unknown error");
}

header('HTTP/1.1 400 Error');
exit("Bad input");
