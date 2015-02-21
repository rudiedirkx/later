<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $id ? $db->select('urls', compact('id'))->first() : false;

if ( isset($_POST['url'], $_POST['title'], $_POST['group']) ) {
	$url = trim($_POST['url']);
	$title = trim($_POST['title']);
	$group = trim($_POST['group']);

	if ( !preg_match('#^\w+://#', $url) ) {
		$url = 'http://' . $url;
	}

	if ( !($_url = parse_url($url)) || !@$_url['host'] ) {
		exit('Invalid URL');
	}

	do_save($url, $title, $id, $group);

	if ( $id && @$_POST['actualize'] ) {
		$db->update('urls', array('o' => time()), array('user_id' => $user->id, 'id' => $id, 'archive' => 0));
	}

	do_redirect('index');
	exit('Bookmark saved');
}

require 'tpl.header.php';

$index = $bm ? $db->count('urls', 'archive = ? AND o >= ?', array(0, $bm->o)) : -1;

?>
<form method="post" action novalidate autocomplete="off">
	<p class="form-item">
		URL:
		<input autofocus type="url" name="url" placeholder="Mandatory URL..." required value="<?= html(@$bm->url) ?>" />
	</p>
	<p class="form-item">
		Title:
		<input name="title" placeholder="Optional title..." value="<?= html(@$bm->title) ?>" />
	</p>
	<p class="form-item">
		Group:
		<input name="group" placeholder="Optional group..." value="<?= html(@$bm->group) ?>" />
	</p>
	<p>
		<input type="submit" value="Save" />
		<? if ($bm && !$bm->archive): ?>
			&nbsp;
			<label><input type="checkbox" name="actualize" /> Move to top (currently ~<?= nth($index) ?>)?</label>
		<? endif ?>
	</p>
</form>

<p><input type="checkbox" onchange="document.querySelector('form').autocomplete = this.checked ? 'on' : 'off'" /> Textfield autocomplete?</p>
