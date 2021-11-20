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

	$db->update('urls', array('group' => trim($_GET['group']) ?: null), array('user_id' => $user->id, 'id' => $_GET['id'], 'archive' => 0));
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

$filter = trim(@$_GET['url_filter']);
$filter = $filter ? $db->replaceholders('AND (u.url LIKE ? OR u.title LIKE ?)', ["%$filter%", "%$filter%"]) : '';

$groupFilter = trim(@$_GET['group_filter']);
$groupFilter = $groupFilter ? $db->replaceholders('AND u.`group` = ?', [$groupFilter]) : '';

$limit = LATER_LIMIT;
$page = (int)@$_GET['page'];
$offset = $page * $limit;
$bookmarks = $db->fetch("
	SELECT u.*, COALESCE(MAX(o.o), u.o) use_o
	FROM urls AS u
	LEFT JOIN urls o ON o.`group` = u.`group` AND o.archive = '0'
	WHERE u.user_id = ? AND u.archive = '0' $filter $groupFilter
	GROUP BY u.id
	ORDER BY u.favorite DESC, use_o DESC, u.o DESC
	LIMIT $limit OFFSET $offset
", array($user->id));
$bookmarks = $bookmarks->all();

$groups = do_groups($bookmarks);

$groupTotals = $db->select_fields('urls', '`group`, COUNT(1)', "user_id = ? AND archive = '0' AND `group` is not null GROUP BY `group` ORDER BY `group` ASC", [$user->id]);

$total = $db->count('urls AS u', "u.user_id = ? AND u.archive = '0' $filter $groupFilter", [$user->id]);
$realTotal = $filter || $groupFilter ? $db->count('urls', "user_id = ? AND archive = '0'", [$user->id]) : 0;

$groupOptions = array_combine(array_keys($groupTotals), array_keys($groupTotals));

require 'tpl.header.php';

echo '<h3>';
echo count($bookmarks) . ' / ' . $total;
echo $realTotal ? ' (' . $realTotal . ')' : '';
echo ' unread <a href="form.php">+</a> / ';
echo '<a href="archive.php">...</a> / ';
require 'tpl.inline-filter.php';
echo '</h3>';

$inner = false;
require 'tpl.bookmarks.php';

?>
<script>
function corruptFormState() {
	return [].some.call(document.querySelectorAll('.change-group > select'), function(el) {
		const li = el.closest('li[data-selected-group]');
		if (el.value != li.dataset.selectedGroup) {
			return true;
		}
	});
}
window.addEventListener('load', function(e) {
	var timer = setInterval(function() {
		if (corruptFormState()) {
			clearInterval(timer);
			location.reload();
		}
	}, 300);
});
</script>
<?php

$https = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off';
$protocol = $https ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['SCRIPT_NAME']);
$base = $protocol . '://' . $domain . str_replace('//', '/', $path . '/') . 'bookmarklet.php';
$bookmarklet = preg_replace('#[\r\n\t]#', '', str_replace('__BASE__', $base, file_get_contents(__DIR__ . '/bookmarklet.js')));

?>

<p>
	<a href="<?= html($bookmarklet) ?>">Drag this to your bookmarks</a>
	or
	<a href onclick="prompt('Copy this:', '<?= html(addslashes($bookmarklet)) ?>'); return false">click to copy</a>
</p>

<?php

require 'tpl.footer.php';
