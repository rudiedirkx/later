<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$id = (int)@$_GET['id'];
$bm = $id ? $db->select('urls', compact('id'))->first() : false;

if ( !$bm || !LATER_READABILITY_PARSER_API_TOKEN ) {
	exit("Nothing here...");
}

// Fetch parsed article via Readability Parses API, or cached
if ( file_exists($cacheFile = LATER_READABILITY_RESPONSE_CACHE . '/' . sha1($bm->url) . '.json') ) {
	$response = file_get_contents($cacheFile);
}
else {
	$query = http_build_query(array(
		'token' => LATER_READABILITY_PARSER_API_TOKEN,
		'url' => $bm->url,
	));
	$response = @file_get_contents('https://readability.com/api/content/v1/parser?' . $query, false, $fgcContext);
	if ( $response && is_writable(dirname($cacheFile)) ) {
		@file_put_contents($cacheFile, $response);
	}
}

if ( !$response ) {
	exit("Can't download JSON");
}

$response = json_decode($response);

require 'tpl.header.php';

?>
<style>
* {
	line-height: 1.3;
}
body {
	font-family: sans-serif;
}
.author-date {
	margin: 0;
}
h1 {
	margin: .5em 0;
}
pre {
	white-space: pre-wrap;
	word-break: break-all;
}
img {
	max-width: 100%;
	width: auto;
	height: auto;
}
</style>
<?php



// Summary
// echo '<p>' . html(strip_tags($response->dek)) . '</p>';

// Author & date
echo '<p class="author-date">' . trim(html(preg_replace('#\d.+#', '', $response->author)) . ' - ' . html($response->date_published), ' -') . '</p>';

// Title
echo '<h1><a href="' . html($bm->url) . '">' . html($response->title) . '</a></h1>';

// Body
$html = $response->content;
// Strip tags
$tags = array('a', 'abbr', 'acronym', 'address', 'article', 'aside', 'b', 'bdi', 'bdo', 'big', 'blockquote', 'br', 'caption', 'cite', 'code', 'col', 'colgroup', 'command', 'dd', 'del', 'details', 'dfn', 'div', 'dl', 'dt', 'em', 'figcaption', 'figure', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hgroup', 'hr', 'i', 'img', 'ins', 'kbd', 'li', 'mark', 'menu', 'meter', 'nav', 'ol', 'output', 'p', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'small', 'span', 'strong', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'time', 'tr', 'tt', 'u', 'ul', 'var', 'wbr');
$tags = implode(array_map(function($tag) {
	return '<' . $tag . '><' . $tag . '/>';
}, $tags));
$html = strip_tags($html, $tags);
// Parse img[srcset]
// $html = preg_replace('#srcset="([^\s,]+)[^"]+"#', 'src="$1"', $html);
// Print
echo $html;



echo '<details>';
echo '<summary>Debug</summary>';
echo '<pre>';
unset($response->content, $response->title);
echo html(print_r($response, 1));
echo '</pre>';
echo '</details>';
