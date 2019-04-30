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
.bookmark.active {
	background-color: #eee;
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

function activateBookmark(bookmark) {
	document.querySelectorAll('.bookmark.active').forEach(el => el.classList.remove('active'));
	bookmark.classList.add('active');
	bookmark.scrollIntoViewIfNeeded();
}

function findAndActivateBookmark(intro) {
	document.querySelectorAll('.bookmark').forEach(el => {
		if (el.textContent == intro) {
			activateBookmark(el);
		}
	});
}

$content.addEventListener('click', function(e) {
	var bookmark = e.target.closest('a.bookmark');
	if (bookmark) {
		e.preventDefault();
		localStorage.readingParagraph = bookmark.textContent;
		activateBookmark(bookmark);
	}
});

Mercury.parse(location.href.replace(/read\.php/, 'source.php'), {contentType: 'text'}).then(rsp => {
	console.log('Mercury rsp', rsp);

	$title.textContent = rsp.title;

	var text = rsp.content;
	text = text.replace(/([\.\?]["”“)]?)([A-Z])/g, '$1\n$2');
	$content.innerText = text;
	$content.innerHTML = '<p>' + $content.innerHTML.replace(/<br>/g, '<p>');

	[].forEach.call($content.children, p => {
		if (p.textContent.length >= 20 && p.innerHTML.substr(0, 20) === p.textContent.substr(0, 20)) {
			const m = p.textContent.match(/^(.{20,}?) (.+)$/);
			if (m) {
				p.innerHTML = `<a href class="bookmark">${m[1]}</a>${p.innerHTML.substr(m[1].length)}`;
			}
			else {
				console.warn('unbookmarkable', p);
			}
		}
	});

	setTimeout("findAndActivateBookmark(localStorage.readingParagraph)");

	delete rsp.content;
	$debug.textContent = JSON.stringify(rsp, null, '  ');
});
</script>
