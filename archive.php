<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$filter = trim(@$_GET['url_filter']);
$filter = $filter ? $db->replaceholders(' AND (url LIKE ? OR title LIKE ?)', array('%' . $filter . '%', '%' . $filter . '%')) : '';

$limit = 100;
$page = (int)@$_GET['page'];
$offset = $page * $limit;
$bookmarks = $db->select('urls', 'user_id = ? AND archive = ?' . $filter . ' ORDER BY o DESC LIMIT ' . $limit . ' OFFSET ' . $offset, array($user->id, 1));
$bookmarks = $bookmarks->all();

$total = $db->count('urls', 'user_id = ? AND archive = ?' . $filter, array($user->id, 1));
$realTotal = $filter ? $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 1)) : 0;

require 'tpl.header.php';

echo '<h3>';
echo count($bookmarks) . ' / ';
echo $total;
echo $realTotal ? ' (' . $realTotal . ')' : '';
echo ' archived <a href="form.php">+</a> / ';
echo '<a href="index.php">...</a> / ';
require 'tpl.inline-filter.php';
echo '</h3>';

$inner = false;
require 'tpl.bookmarks.php';

require 'tpl.footer.php';
