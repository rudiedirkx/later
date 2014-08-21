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

// Custom auth handler to throw a 404
if ( !is_logged_in(false) ) {
	header('HTTP/1.1 401 Unauthorized', true, 401);
	exit("You must be logged in to do this...");
}

echo "I AM A LIVE WEBHOOK\n\n";

$body = trim(@$_REQUEST['title'] . "\n" . @$_REQUEST['notes']);

/* DEBUG *
$body = 'Foo foo foo

http://oele.boele.com/blaaa';
/* DEBUG */

if ( preg_match('#https?://\S+#', $body, $match, PREG_OFFSET_CAPTURE) ) {
	$url = $match[0][0];
	$title = trim(substr($body, 0, $match[0][1]));

	do_save($url, $title);

	exit('OK');
}

exit("Not good ennough input...");
