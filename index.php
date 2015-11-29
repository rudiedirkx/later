<?php

require 'inc.bootstrap.php';

is_logged_in(true);

if ( isset($_GET['archive']) ) {
	do_tokencheck('archive');

	$db->update('urls', array('archive' => 1, 'o' => time()), array('user_id' => $user->id, 'id' => $_GET['archive']));
	exit('OK');
}

else if ( isset($_GET['unarchive']) ) {
	do_tokencheck('unarchive');

	$db->update('urls', array('archive' => 0, 'o' => time()), array('user_id' => $user->id, 'id' => $_GET['unarchive']));
	exit('OK');
}

else if ( isset($_GET['favorite'], $_GET['value']) ) {
	do_tokencheck('favorite');

	$db->update('urls', array('favorite' => (int)(bool)$_GET['value']), array('user_id' => $user->id, 'id' => $_GET['favorite']));
	exit('OK');
}

else if ( isset($_GET['group'], $_GET['id']) ) {
	do_tokencheck('group');

	$db->update('urls', array('group' => $_GET['group'] ?: ''), array('user_id' => $user->id, 'id' => $_GET['id'], 'archive' => 0));
	exit('OK');
}

else if ( isset($_GET['actualize']) ) {
	do_tokencheck('actualize');

	$db->update('urls', array('o' => time()), array('user_id' => $user->id, 'id' => $_GET['actualize'], 'archive' => 0));
	exit('OK');
}

else if ( isset($_GET['group'], $_GET['hidden']) ) {
	do_tokencheck('toggleGroup');

	if ( $_GET['hidden'] ) {
		$user->hide_groups[] = $_GET['group'];
		$user->hide_groups = array_values(array_unique($user->hide_groups));
	}
	else {
		if ( ($index = array_search($_GET['group'], $user->hide_groups)) !== false ) {
			array_splice($user->hide_groups, $index, 1);
		}
	}

	$db->update('users', array('hide_groups' => implode(',', $user->hide_groups)), array('id' => $user->id));
	exit('OK');
}

$urlFilter = trim(@$_GET['url_filter']) ? $db->replaceholders(' AND url LIKE ?', array('%' . trim($_GET['url_filter']) . '%')) : '';
$groupFilter = trim(@$_GET['group_filter']) ? $db->replaceholders(' AND `group` = ?', array(trim($_GET['group_filter']))) : '';

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
		" . $groupFilter . "
	ORDER BY
		IF(u.`group` = '' OR u.`group` IS NULL, u.o, (SELECT MAX(o) FROM urls WHERE `group` = u.`group` AND archive = '0')) DESC,
		u.o DESC
	LIMIT " . $limit . "
	OFFSET " . $offset . "
", array($user->id));
$bookmarks = $bookmarks->all();

$groups = do_groups($bookmarks);

$groupTotals = $db->select_fields('urls', '"group", COUNT(1)', 'archive = ? AND "group" <> ? GROUP BY "group"', array(0, ''));

$total = $db->count('urls', 'user_id = ? AND archive = ?' . $urlFilter . $groupFilter, array($user->id, 0));
$realTotal = $urlFilter || $groupFilter ? $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 0)) : 0;

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
$path = dirname($_SERVER['SCRIPT_NAME']);
$bookmarklet = "javascript: (document.head||document.documentElement).appendChild((function(el) { el.src='" . $protocol . "://" . $domain . str_replace('//', '/', $path . '/') . "bookmarklet.php?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title); return el; })((document.createElement||Document.prototype.createElement).call(document, 'script'))); void(0)";

?>

<p><a href="<?= $bookmarklet ?>">Drag this to your bookmarks</a> or (<a href onclick="prompt('Copy this:', '<?= addslashes($bookmarklet) ?>'); return false">copy it</a>)</p>
