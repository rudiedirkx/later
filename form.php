<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $db->select('urls', compact('id'))->first() ?: new stdClass;

if ( isset($_POST['url'], $_POST['title'], $_POST['group']) ) {
	$url = trim($_POST['url']);
	$title = trim($_POST['title']);
	$group = trim($_POST['group']);

	if ( !($_url = parse_url($url)) || !@$_url['host'] ) {
		exit('Invalid URL');
	}

	do_save($url, $title, $id, $group);

	do_redirect('index');
	exit;
}

require 'tpl.header.php';

?>
<form method="post" action>
	<p class="form-item">Title: <input name="title" placeholder="Optional title..." value="<?= @$bm->title ?>" /></p>
	<p class="form-item">URL: <input type="url" name="url" placeholder="Mandatory URL..." required value="<?= @$bm->url ?>" /></p>
	<p class="form-item">Group: <input name="group" placeholder="Optional group..." value="<?= @$bm->group ?>" /></p>
	<p><input type="submit" value="Save" /></p>
</form>
