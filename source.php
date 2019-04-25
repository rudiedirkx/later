<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $id ? $db->select('urls', compact('id'))->first() : false;

if ( !$bm ) {
	exit("Nothing here...");
}

$html = file_get_contents($bm->url);
$html = preg_replace('#<script.+</script>#i', '', $html);
$html = preg_replace('#(src|srcset|href)="/#i', 'xxx="/', $html);
echo $html;
