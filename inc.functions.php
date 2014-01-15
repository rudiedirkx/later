<?php

function do_groups($bookmarks) {
	$groups = array();
	foreach ( $bookmarks as $bm ) {
		if ( $bm->group ) {
			$groups[$bm->group][] = $bm;
		}
		else {
			$groups[$bm->id] = $bm;
		}
	}
	return $groups;
}

function do_logout() {
	if ( isset($_SESSION[SESSION_NAME]) ) {
		unset($_SESSION[SESSION_NAME]);
	}
}

function html( $text ) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function get_css() {
	$file = __DIR__ . '/style.css';
	$css = trim(file_get_contents($file));
	$css = preg_replace('/(\t|(?<!})(?:\r\n|\n))+/', ' ', $css);
	// $css = preg_replace('/[\s\r\n]+/', '', $css);
	return $css;
}

function do_save( $url, $title, $id = null, $group = '' ) {
	global $db, $user, $fgcContext;

	if ( !$title ) {
		$html = @file_get_contents($url, false, $fgcContext);
		if ( $html && @preg_match('#<title>([^<]+)#', $html, $match) ) {
			$title = $match[1];
		}
	}

	$title = preg_replace('#\s+#', ' ', trim($title));

	$save = array('title' => $title, 'url' => $url);
	$group and $save['group'] = $group;

	// Given id, only update, no extra logic, like order or archive
	if ( $id ) {
		try {
			return $db->update('urls', $save, array('id' => $id));
		}
		catch (db_exception $ex) {
			echo $ex->query . "\n\n";
			echo $ex->getMessage() . "\n";
			exit;
		}
	}

	$save += array('o' => time());

	// Exists, so reorder and unarchive
	if ( $id = $db->select_one('urls', 'id', array('url' => $url, 'user_id' => $user->id)) ) {
		return $db->update('urls', $save + array('archive' => 0), array('id' => $id));
	}

	// New, so insert
	$db->insert('urls', array('url' => $url, 'created' => time(), 'user_id' => $user->id) + $save);
}

function get_url( $path, $query = array() ) {
	$query = $query ? '?' . http_build_query($query) : '';
	$path = $path ? $path . '.php' : basename($_SERVER['SCRIPT_NAME']);
	return $path . $query;
}

function do_redirect( $path, $query = array() ) {
	$url = get_url($path, $query);
	header('Location: ' . $url);
}

function is_logged_in( $act = true ) {
	global $db, $user;
	if ( $user ) {
		return true;
	}

	$cookie = isset($_SESSION[SESSION_NAME]['uid']);
	if ( $cookie ) {
		$id = $_SESSION[SESSION_NAME]['uid'];
		$user = $db->select('users', compact('id'))->first();
		if ( $user ) {
			return true;
		}
	}

	if ( $act ) {
		do_logout();

		if ( !$cookie ) {
			do_redirect('login');
		}
		exit('You no logged in no more. <a href="logout.php">Log out for sure.</a>');
	}
}
