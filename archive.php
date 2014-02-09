<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$limit = 100;
$page = (int)@$_GET['page'];
$offset = $page * $limit;
$bookmarks = $db->select('urls', 'user_id = ? AND archive = ? ORDER BY o DESC LIMIT ' . $limit . ' OFFSET ' . $offset, array($user->id, 1));
$bookmarks = $bookmarks->all();

$total = $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 1));

require 'tpl.header.php';

echo '<h3>' . count($bookmarks) . ' / ' . $limit . ' / ' . $total . ' archived <a href="form.php">+</a> / <a href="index.php">...</a></h3>';

require 'tpl.bookmarks.php';
