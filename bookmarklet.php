<?php

require 'inc.bootstrap.php';

$page = !empty($_GET['page']);

$loggedIn = is_logged_in(false);

$color = 'red';
$text = 'Saved!';
$hide = 3000000;

if ( $loggedIn ) {
	if ( isset($_GET['url'], $_GET['title']) ) {

		if ( do_save($_GET['url'], $_GET['title']) ) {
			$color = 'green';
			$hide = 3000;
		}

		// Wrong URL, probably
		else {
			$text = "NOT saved. Wrong URL?";
		}

	}

	// Missing parameters
	else {
		$text = "Missing parameters.";
	}
}

// Not logged in
else {
	$text = "You're not logged in.";
}

if ( $page ) {
	echo "<p>$text</p>\n";
	echo "<a href=/>Home</a>\n";
	exit;
}

header('Content-type: application/javascript');

?>

(function(loading) {

	if (loading = document.querySelector('.later-loading')) {
		loading.parentNode.removeChild(loading);
	}

	var div = document.createElement('div');
	div.className = 'later-loaded';



	div.textContent = '<?= addslashes($text) ?>';



	div.setAttribute('style', 'z-index: 2000999999; position: fixed; left: 20px; top: 50px; border: solid 20px <?= $color ?>; padding: 30px 20px; background: white; color: black; font-size: 30px; cursor: pointer; transition: opacity 500ms linear');
	div.onclick = function(e) {
		this.parentNode.removeChild(this);
	};
	document.body.insertBefore(div, document.body.firstElementChild);
	setTimeout(function() {
		try {
			div.style.opacity = 0;
			setTimeout(function() {
				div.remove();
			}, 600);
		} catch (ex) {}
	}, <?= $hide ?>);

})();
