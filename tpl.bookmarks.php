<?php

$_list = @$groups ?: $bookmarks;

echo '<ol class="bookmarks">';
foreach ( $_list as $g => $bm ) {
	if ( is_array($bm) ) {
		$groups = $bm;
		echo '<li class="multiple">';
		$groupTotal = isset($groupTotals[$g]) ? ' (' . (count($bm) < $groupTotals[$g] ? count($bm) . ' / ' : '') . $groupTotals[$g] . ')' : '';
		$filter = '<a class="filter" href="?group_filter=' . urlencode($g) . '">filter</a>';
		echo '<div class="group-header">' . $g . $groupTotal . ' (' . $filter . ')</div>';
		$inner = true;
		require 'tpl.bookmarks.php';
		$inner = false;
		echo '</li>';
		continue;
	}

	$id = $bm->id;

	$_url = parse_url($bm->url);
	$host = substr($_url['host'], 0, 4) == 'www.' ? substr($_url['host'], 4) : $_url['host'];

	$classes = array('single');
	$bm->favorite && $classes[] = 'is-favorite';

	$archiveAction = $bm->archive ? 'unarchive' : 'archive';

	echo '<li data-id="' . $bm->id . '" class="' . implode(' ', $classes) . '">';
	echo '<div class="' . $archiveAction . '"><a class="ajax" href="' . get_url('index', array($archiveAction => $id)) . '">A</a></div>';
	echo '<div class="link">';
	echo '  <a href="' . html($bm->url) . '">' . html($bm->title ?: $bm->url) . '</a>';
	echo '  <div class="favorite"><a class="ajax" href="' . get_url('index', array('favorite' => $id, 'value' => (int)!$bm->favorite)) . '">♥</a></div>';
	echo '</div>';
	echo '<div class="created">' . date(DT, $bm->created) . '</div>';
	// if ( $bm->group ) {
		// echo '<div class="group">[' . $bm->group . ']</div>';
	// }
	if (LATER_READABILITY_PARSER_API_TOKEN) {
		echo '<div class="host"><a href="' . get_url('read', array('id' => $id)) . '">' . $host . '</a></div>';
	}
	else {
		echo '<div class="host">' . $host . '</div>';
	}
	if ( @$groupOptions ) {
		echo '<div class="change-group"><select><option value>-</option>' . html_options($groupOptions, $bm->group) . '</select></div>';
	}
	echo '<div class="edit"><a href="' . get_url('form', array('id' => $id)) . '">E</a></div>';
	if ( !$bm->archive ) {
		echo '<div class="actualize"><a class="ajax" href="' . get_url('index', array('actualize' => $id)) . '">▲</a></div>';
	}
	echo '</li>';
}
echo '</ol>';

// No footer for sub-inclusions
if ( @$inner ) {
	return;
}

$pages = ceil($total / $limit);

?>
<?if ($pages > 1): ?>
	<ul class="pager has-<?= $pages ?>-pages">
		<? foreach (range(0, $pages-1) as $p): ?>
			<li class="<?= $p == $page ? 'current' : '' ?>">
				<a href="?page=<?= $p ?>"><?= $p+1 ?></a>
			</li>
		<? endforeach ?>
	</ul>
<? endif ?>

<script>
function rAjax(href, done) {
	var xhr = new XMLHttpRequest;
	xhr.onload = function(e) {
		if ( this.status == 200 && this.responseText == 'OK' ) {
			return (done || function() {
				location.reload();
			})();
		}

		alert('Error: ' + this.responseText);
		document.querySelector('.working').classList.remove('working');
	};
	xhr.open('get', href, true);
	xhr.send();
}
[].forEach.call(document.querySelectorAll('.ajax'), function(el) {
	el.addEventListener('click', function(e) {
		e.preventDefault();
		this.classList.add('working');
		rAjax(el.href);
	});
});

[].forEach.call(document.querySelectorAll('.change-group select'), function(el) {
	el.addEventListener('change', function(e) {
		var group = this.value || '',
			id = this.parentNode.parentNode.getAttribute('data-id'),
			base = '<?= get_url('index', array('group' => 'GROUP', 'id' => 'ID')) ?>',
			href = base.replace('GROUP', encodeURIComponent(group)).replace('ID', id);
		this.classList.add('working');
		rAjax(href);
	});
});
</script>
