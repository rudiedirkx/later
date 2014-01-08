<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $db->select('urls', compact('id'))->first() ?: new stdClass;

if ( isset($_POST['url'], $_POST['title']) ) {
	$url = trim($_POST['url']);
	$title = trim($_POST['title']);

	if ( !($_url = parse_url($url)) || !@$_url['host'] ) {
		exit('Invalid URL');
	}

	do_save($url, $title, $id);

	do_redirect('index');
	exit;
}

require 'tpl.header.php';

?>
<form method="post" action>
	<p class="form-item">Title: <input name="title" placeholder="Optional title..." value="<?= @$bm->title ?>" /></p>
	<p class="form-item">URL: <input type="url" name="url" placeholder="Mandatory URL..." required value="<?= @$bm->url ?>" /></p>
	<p><input type="submit" value="Save" /></p>
</form>
