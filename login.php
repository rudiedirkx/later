<?php

require 'inc.bootstrap.php';

if ( is_logged_in(false) ) {
	do_redirect('index');
	exit("You're already logged in....");
}

if ( isset($_POST['user'], $_POST['pass']) ) {
	setcookie('lt_user', $_POST['user']);

	$args = array($_POST['user'], ':', $_POST['pass']);
	$user = $db
		->select('users', 'email = ? AND password = SHA1(CONCAT(id, ?, ?))', $args)
		->first();

	if ( $user ) {
		@session_start();

		$_SESSION[SESSION_NAME]['uid'] = $user->id;

		do_redirect('index');
		exit;
	}

	echo 'No good.';
	exit;
}

$error = @$_GET['error'];

require 'tpl.header.php';

?>

<? if ($error): ?>
	<p class="error">Error: <?= html($error) ?></p>
<? endif ?>

<form method="post" action>
	<p class="form-item">E-mail: <input type="email" name="user" value="<?= @$_COOKIE['lt_user'] ?>" required autofocus /></p>
	<p class="form-item">Password: <input type="password" name="pass" required /></p>
	<p><input type="submit" value="Log in" /></p>
</form>
