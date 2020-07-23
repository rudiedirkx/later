<?php

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Cookie\CookieJar;
use rdx\jsdom\Node;
// use GuzzleHttp\RedirectMiddleware;

/**
 *
 */
function get_token( $name ) {
	return sha1('later:' . (@$_SESSION[SESSION_NAME]['salt'] ?: rand()) . ':' . $name);
}

/**
 *
 */
function do_tokencheck( $name ) {
	if ( @$_REQUEST['_token'] !== get_token($name) ) {
		exit("Access denied\n");
	}
}

/**
 *
 */
function nth( $n ) {
	$q = (int)substr($n, -1);
	$trailer = 'th';

	switch ( $q ) {
		case 1:
			$trailer = 'st';
			break;
		case 2:
			$trailer = 'nd';
			break;
		case 3:
			$trailer = 'rd';
			break;
	}

	return $n . $trailer;
}

/**
 *
 */
function html_options( $options, $selected = null, $empty = '' ) {
	$html = '';
	$empty && $html .= '<option value="">' . $empty;
	foreach ( $options AS $value => $label ) {
		$isSelected = $value == $selected ? ' selected' : '';
		$html .= '<option value="' . html($value) . '"' . $isSelected . '>' . html($label) . '</option>';
	}
	return $html;
}

/**
 *
 */
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

/**
 *
 */
function do_logout() {
	if ( isset($_SESSION[SESSION_NAME]) ) {
		unset($_SESSION[SESSION_NAME]);
	}
}

/**
 *
 */
function html( $text ) {
	return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8') ?: htmlspecialchars((string)$text, ENT_QUOTES, 'ISO-8859-1');
}

/**
 *
 */
function get_css() {
	$file = __DIR__ . '/style.css';
	$css = trim(file_get_contents($file));
	$css = preg_replace('/(\t|(?<!})(?:\r\n|\n))+/', ' ', $css);
	// $css = preg_replace('/[\s\r\n]+/', '', $css);
	return $css;
}

/**
 *
 */
function get_valid_url( $url, &$_url = null ) {
	return ($_url = parse_url($url)) && !empty($_url['host']);
}

/**
 *
 */
function get_html( $url ) {
	$cookies = new CookieJar();
	$guzzle = new Guzzle([
		'cookies' => $cookies,
		'headers' => ['User-agent' => 'WhatsApp/2.20.108 A'],
		// 'allow_redirects' => [
		// 	'track_redirects' => true,
		// ] + RedirectMiddleware::$defaultSettings,
	]);
	$rsp = $guzzle->get($url);
	return (string) $rsp->getBody();
}

/**
 *
 */
function do_save( $url, $title, $id = null, $group = '' ) {
	global $db, $user, $fgcContext;

	if ( !get_valid_url($url) ) {
		return false;
	}

	if ( !$title ) {
		$html = get_html($url);
		$dom = Node::create($html);

		$el = $dom->query('title');
		if ( $el ) {
			$title = $el->textContent;

			$title = strtr($title, array(
				'&trade;' => '™',
				'&rsquo;' => '’',
			));
			$title = html_entity_decode($title);
			$title = preg_replace_callback('/&#(\d+);/', function($match) {
				return chr((int)$match[1]);
			}, $title);
		}
	}

	$title = preg_replace('#\s+#', ' ', trim($title));

	$save = array(
		'title' => $title,
		'url' => $url,
	);
	$group and $save['group'] = $group;

	// Given id, only update, no extra logic, like order or archive
	if ( $id ) {
		foreach ($GLOBALS['g_bookmarkPreprocessors'] as $preprocessor) {
			$preprocessor->beforeSave($save);
		}

		try {
			return $db->update('urls', $save, array('id' => $id));
		}
		catch (db_exception $ex) {
			return false;
		}
	}

	$save += array('o' => time());

	foreach ($GLOBALS['g_bookmarkPreprocessors'] as $preprocessor) {
		$preprocessor->beforeMatch($save);
	}

	// Find existing bookmark
	$id = null;
	foreach ($GLOBALS['g_bookmarkMatchers'] as $matcher) {
		if ( $id = $matcher->findBookmarkId($save) ) {
			break;
		}
	}

	foreach ($GLOBALS['g_bookmarkPreprocessors'] as $preprocessor) {
		$preprocessor->beforeSave($save);
	}

	if ( $id ) {
		return $db->update('urls', $save + array('archive' => 0), array('id' => $id));
	}

	// New, so insert
	return $db->insert('urls', array('created' => time(), 'user_id' => $user->id) + $save);
}

/**
 *
 */
function get_url( $path, $query = array() ) {
	$query = $query ? '?' . http_build_query($query) : '';
	$path = $path ? $path . '.php' : basename($_SERVER['SCRIPT_NAME']);
	return $path . $query;
}

/**
 *
 */
function do_redirect( $path, $query = array() ) {
	$url = get_url($path, $query);
	header('Location: ' . $url);
}

/**
 *
 */
function is_logged_in( $act = true ) {
	global $db, $user;
	if ( $user ) {
		return true;
	}

	// From session
	$cookie = isset($_SESSION[SESSION_NAME]['uid']);
	if ( $cookie ) {
		$id = $_SESSION[SESSION_NAME]['uid'];
		$user = $db->select('users', compact('id'))->first();
		if ( $user ) {
			$user->hide_groups = array_filter(explode(',', $user->hide_groups ?: ''));
			return true;
		}
	}

	// From HTTP auth
	if ( isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ) {
		$args = array($_SERVER['PHP_AUTH_USER'], ':', $_SERVER['PHP_AUTH_PW']);
		$user = $db
			->select('users', 'email = ? AND password = SHA1(CONCAT(id, ?, ?))', $args)
			->first();
		if ( $user ) {
			$user->hide_groups = array_filter(explode(',', $user->hide_groups ?: ''));
			return true;
		}
	}

	// FAILED
	if ( $act ) {
		do_logout();

		if ( !$cookie ) {
			do_redirect('login');
		}
		exit('You no logged in no more. <a href="logout.php">Log out for sure.</a>');
	}
}
