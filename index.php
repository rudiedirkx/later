<?php

require 'inc.bootstrap.php';

is_logged_in(true);

if ( isset($_GET['archive']) ) {
	$db->update('urls', array('archive' => 1, 'o' => time()), array('user_id' => $user->id, 'id' => $_GET['archive']));
	exit('OK');
}

else if ( isset($_GET['unarchive']) ) {
	$db->update('urls', array('archive' => 0, 'o' => time()), array('user_id' => $user->id, 'id' => $_GET['unarchive']));
	exit('OK');
}

else if ( isset($_GET['favorite'], $_GET['value']) ) {
	$db->update('urls', array('favorite' => (int)(bool)$_GET['value']), array('user_id' => $user->id, 'id' => $_GET['favorite']));
	exit('OK');
}

$limit = 20;
$bookmarks = $db->select('urls', 'user_id = ? AND archive = ? ORDER BY o DESC LIMIT ' . $limit, array($user->id, 0));
$bookmarks = $bookmarks->all();
$groups = do_groups($bookmarks);

$total = $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 0));

require 'tpl.header.php';

echo '<h3>' . count($bookmarks) . ' / ' . $limit . ' / ' . $total . ' unread <a href="form.php">+</a> / <a href="archive.php">...</a></h3>';

require 'tpl.bookmarks.php';

$https = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off';
$protocol = $https ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$bookmarklet = "javascript: document.head.appendChild((function(el) { el.src='" . $protocol . "://" . $domain . "/later/bookmarklet.php?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title); return el; })(document.createElement('script'))); void(0)";

?>

<p><a href="<?= $bookmarklet ?>">Bookmarklet</a> (<a href onclick="prompt('Copy this:', '<?= addslashes($bookmarklet) ?>'); return false">copy</a>)</p>
