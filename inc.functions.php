<?php

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

function do_save( $url, $title ) {
	global $db, $user;

	$save = array('o' => time(), 'title' => $title);

	// Exists, so update order
	if ( $id = $db->select_one('urls', 'id', array('url' => $url, 'user_id' => $user->id)) ) {
		return $db->update('urls', $save + array('archive' => 0), array('id' => $id));
	}

	// New, so insert
	$db->insert('urls', array('url' => $url, 'created' => time(), 'user_id' => $user->id) + $save);
}

function get_url( $path, $query = array() ) {
	$query = $query ? '?' . http_build_query($query) : '';
	return $path . '.php' . $query;
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

	if ( isset($_SESSION[SESSION_NAME]['uid']) ) {
		$id = $_SESSION[SESSION_NAME]['uid'];
		$user = $db->select('users', compact('id'))->first();
		if ( $user ) {
			return true;
		}
	}

	if ( $act ) {
		do_logout();

		// do_redirect('login', array('error' => 'From: ' . __FUNCTION__ . '()'));
		exit('You no logged in?! <a href="logout.php">Log out</a>');
	}
}
