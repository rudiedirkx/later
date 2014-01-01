<?php

require 'inc.bootstrap.php';

is_logged_in(true);

if ( isset($_GET['archive']) ) {
	$db->update('urls', array('archive' => 1), array('user_id' => $user->id, 'id' => $_GET['archive']));
	exit('OK');
}

$bookmarks = $db->select('urls', 'user_id = ? AND archive = ? ORDER BY o DESC LIMIT 20', array($user->id, 0));
$bookmarks = $bookmarks->all();

$total = $db->count('urls', 'user_id = ? AND archive = ?', array($user->id, 0));

?>
<title>Later</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<meta name="viewport" content="width=device-width, initial-scale=0.5" />
<meta charset="utf-8" />
<style><?= get_css() ?></style>
<?php

echo '<h3>' . count($bookmarks) . ' / ' . $total . ' bookmarks <a href="add.php">+</a></h3>';
echo '<ol class="bookmarks">';
foreach ( $bookmarks as $bm ) {
	$id = $bm->id;

	$_url = parse_url($bm->url);
	$host = substr($_url['host'], 0, 4) == 'www.' ? substr($_url['host'], 4) : $_url['host'];
// var_dump($host);

	$classes = array();
	$bm->favorite && $classes[] = 'is-favorite';

	echo '<li class="' . implode(' ', $classes) . '">';
	echo '<div class="archive"><a class="ajax" href="?archive=' . $id . '">A</a></div>';
	echo '<div class="link">';
	echo '  <a href="' . html($bm->url) . '">' . html($bm->title) . '</a>';
	echo '  <div class="favorite"><a href="?favorite=' . $id . '">♥</a></div>';
	echo '</div>';
	echo '<div class="created">' . date(DT, $bm->created) . '</div>';
	echo '<div class="host">' . $host . '</div>';
	// echo '<div class="edit"><a href="https://www.instapaper.com/edit/' . $id . '">E</a></div>';
	echo '</li>';
}
echo '</ol>';



$https = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off';
$protocol = $https ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$bookmarklet = "javascript: document.head.appendChild((function(el) { el.src='" . $protocol . "://" . $domain . "/later/bookmarklet.php?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title); return el; })(document.createElement('script'))); void(0)";

?>

<p><a href="<?= $bookmarklet ?>">Bookmarklet</a> (<a href onclick="prompt('Copy this:', '<?= addslashes($bookmarklet) ?>'); return false">copy</a>)</p>

<script>
[].forEach.call(document.querySelectorAll('.ajax'), function(el) {
	el.addEventListener('click', function(e) {
		e.preventDefault();
		var xhr = new XMLHttpRequest;
		xhr.onload = function(e) {
			if ( this.status == 200 && this.responseText == 'OK' ) {
				return location.reload();
			}

			alert('Error: ' + this.responseText);
		};
		xhr.open('get', el.href, true);
		xhr.send();
	});
});
</script>
