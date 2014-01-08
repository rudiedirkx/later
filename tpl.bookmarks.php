<?php

echo '<ol class="bookmarks">';
foreach ( $bookmarks as $bm ) {
	$id = $bm->id;

	$_url = parse_url($bm->url);
	$host = substr($_url['host'], 0, 4) == 'www.' ? substr($_url['host'], 4) : $_url['host'];

	$classes = array();
	$bm->favorite && $classes[] = 'is-favorite';

	$archiveAction = $bm->archive ? 'unarchive' : 'archive';

	echo '<li class="' . implode(' ', $classes) . '">';
	echo '<div class="' . $archiveAction . '"><a class="ajax" href="' . get_url('index', array($archiveAction => $id)) . '">A</a></div>';
	echo '<div class="link">';
	echo '  <a href="' . html($bm->url) . '">' . html($bm->title ?: $bm->url) . '</a>';
	echo '  <div class="favorite"><a class="ajax" href="' . get_url('index', array('favorite' => $id, 'value' => (int)!$bm->favorite)) . '">♥</a></div>';
	echo '</div>';
	echo '<div class="created">' . date(DT, $bm->created) . '</div>';
	echo '<div class="host"><a href="/readre/read.php?url=' . urlencode($bm->url) . '">' . $host . '</a></div>';
	echo '<div class="edit"><a href="' . get_url('form', array('id' => $id)) . '">E</a></div>';
	echo '</li>';
}
echo '</ol>';

?>
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
