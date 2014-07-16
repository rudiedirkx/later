<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$urlFilter = @$_GET['url_filter'] && trim($_GET['url_filter']) != '' ? $db->replaceholders(' AND url LIKE ?', array('%' . trim($_GET['url_filter']) . '%')) : '';

$limit = 100;
$page = (int)@$_GET['page'];
$offset = $page * $limit;
$bookmarks = $db->select('urls', 'user_id = ? AND archive = ?' . $urlFilter . ' ORDER BY o DESC LIMIT ' . $limit . ' OFFSET ' . $offset, array($user->id, 1));
$bookmarks = $bookmarks->all();

$total = $db->count('urls', 'user_id = ? AND archive = ?' . $urlFilter, array($user->id, 1));

require 'tpl.header.php';

echo '<h3>';
echo count($bookmarks) . ' / ';
echo $total . ' archived <a href="form.php">+</a> / ';
echo '<a href="index.php">...</a> / ';
echo '<form class="inline-filter"><input name="url_filter" placeholder="Filter URL..." class="search" /> <input type="submit" class="submit" /></form>';
echo '</h3>';

require 'tpl.bookmarks.php';
