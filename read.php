<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $id ? $db->select('urls', compact('id'))->first() : false;

if ( !$bm ) {
	exit("Nothing here...");
}

require 'tpl.header.php';

?>
<style>
br {
	margin: 1em 0;
}
</style>

<h1><a id="title" href="<?= html($bm->url) ?>"></a></h1>

<div id="content">Loading...</div>

<details>
	<summary>Debug</summary>
	<pre id="debug"></pre>
</details>

<!-- <script src="node_modules/@postlight/mercury-parser/dist/mercury.web.js"></script> -->
<script src="https://unpkg.com/@postlight/mercury-parser@2.1.0/dist/mercury.web.js"></script>
<script>
const $title = document.querySelector('#title');
const $content = document.querySelector('#content');
const $debug = document.querySelector('#debug');

Mercury.parse(location.href.replace(/read\.php/, 'source.php'), {contentType: 'text'}).then(rsp => {
	console.log(rsp);

	$title.textContent = rsp.title;

	var text = rsp.content;
	text = text.replace(/([\.\?]["”“]?)(\w)/g, '$1\n$2');
	$content.innerText = text;
	$content.innerHTML = '<p>' + $content.innerHTML.replace(/<br>/g, '<p>');

	delete rsp.content;
	$debug.textContent = JSON.stringify(rsp, null, '  ');
});
</script>
