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

else if ( isset($_GET['group'], $_GET['id']) ) {
	$db->update('urls', array('group' => $_GET['group'] ?: ''), array('user_id' => $user->id, 'id' => $_GET['id'], 'archive' => 0));
	exit('OK');
}

else if ( isset($_GET['actualize']) ) {
	$db->update('urls', array('o' => time()), array('user_id' => $user->id, 'id' => $_GET['actualize'], 'archive' => 0));
	exit('OK');
}

$urlFilter = @$_GET['url_filter'] && trim($_GET['url_filter']) != '' ? $db->replaceholders(' AND url LIKE ?', array('%' . trim($_GET['url_filter']) . '%')) : '';

$limit = 25;
$page = (int)@$_GET['page'];
$offset = $page * $limit;
$bookmarks = $db->fetch("
	SELECT *
	FROM urls u
	WHERE
		u.user_id = ? AND
		u.archive = '0'
		" . $urlFilter . "
	ORDER BY
		IF(u.`group` = '' OR u.`group` IS NULL, u.o, (SELECT MAX(o) FROM urls WHERE `group` = u.`group` AND archive = '0')) DESC,
		u.o DESC
	LIMIT " . $limit . "
	OFFSET " . $offset . "
", array($user->id));
$bookmarks = $bookmarks->all();

$groups = do_groups($bookmarks);

$total = $db->count('urls', 'user_id = ? AND archive = ?' . $urlFilter, array($user->id, 0));
$realTotal = $urlFilter ? $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 0)) : 0;

$groupOptions = $db->select_fields('urls', '"group", "group"', 'archive = ? AND "group" <> ? GROUP BY "group"', array(0, ''));

require 'tpl.header.php';

echo '<h3>';
echo count($bookmarks) . ' / ';
echo $total;
echo $realTotal ? ' (' . $realTotal . ')' : '';
echo ' unread <a href="form.php">+</a> / ';
echo '<a href="archive.php">...</a> / ';
require 'tpl.inline-filter.php';
echo '</h3>';

require 'tpl.bookmarks.php';

$https = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off';
$protocol = $https ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$bookmarklet = "javascript: document.head.appendChild((function(el) { el.src='" . $protocol . "://" . $domain . "/later/bookmarklet.php?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title); return el; })(document.createElement('script'))); void(0)";

?>

<p><a href="<?= $bookmarklet ?>">Bookmarklet</a> (<a href onclick="prompt('Copy this:', '<?= addslashes($bookmarklet) ?>'); return false">copy</a>)</p>
