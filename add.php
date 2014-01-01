<?php

require 'inc.bootstrap.php';

is_logged_in(true);

if ( isset($_POST['url'], $_POST['title']) ) {
	$url = $_POST['url'];
	$title = $_POST['title'];

	if ( !($_url = parse_url($url)) || !@$_url['host'] ) {
		exit('Invalid URL');
	}

	if ( !$title ) {
		$html = file_get_contents($url);
		if ( !preg_match('#<title>(.+?)</title>#', $html, $match) ) {
			exit("Couldn't find title in live document. Gimme title!");
		}

		$title = $match[1];
	}

	do_save($url, $title);

	do_redirect('index');
	exit;
}

?>
<title>Later</title>
<style><?= get_css() ?></style>

<form method="post" action>
	<p>Title: <input name="title" placeholder="Optional title..." /></p>
	<p>URL: <input type="url" name="url" placeholder="Mandatory URL..." required /></p>
	<p><input type="submit" value="Save" /></p>
</form>
