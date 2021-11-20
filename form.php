<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $id ? $db->select('urls', compact('id'))->first() : false;

if ( isset($_POST['url'], $_POST['title'], $_POST['group']) ) {
	do_tokencheck('save:' . $id);

	$url = trim($_POST['url']);
	$title = trim($_POST['title']);
	$group = trim($_POST['group']) ?: null;

	// Make it start with http:// if no scheme was provided
	if ( !preg_match('#^\w+://#', $url) ) {
		$url = 'http://' . $url;
	}

	// Must be URL-parsable, no crazy stuff allowed
	if ( !get_valid_url($url) ) {
		exit('Invalid URL');
	}

	// Insert or update
	do_save($url, $title, $id, $group);

	// Move to top
	if ( $id && !empty($_POST['actualize']) ) {
		$db->update('urls', array('o' => time()), array('user_id' => $user->id, 'id' => $id, 'archive' => 0));
	}

	do_redirect('index');
	exit('Bookmark saved');
}

require 'tpl.header.php';

$index = $bm ? $db->count('urls', 'archive = ? AND o >= ?', array(0, $bm->o)) : -1;
$groupOptions = $db->select_fields('urls', '"group", "group"', 'archive = ? AND "group" <> ? GROUP BY "group"', array(0, ''));

?>
<form method="post" action novalidate autocomplete="off">
	<input type="hidden" name="_token" value="<?= get_token('save:' . $id) ?>" />

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
		<input name="group" placeholder="Optional group..." value="<?= html(@$bm->group) ?>" list="groups" />
		<datalist id="groups">
			<?= html_options($groupOptions) ?>
		</datalist>
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
