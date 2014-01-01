<?php

require 'inc.bootstrap.php';

if ( isset($_POST['user'], $_POST['pass']) ) {
	setcookie('lt_user', $_POST['user']);

	$args = array($_POST['user'], ':', $_POST['pass']);
	$user = $db
		->select('users', 'email = ? AND password = SHA1(CONCAT(id, ?, ?))', $args)
		->first();

	if ( $user ) {
		session_start();

		$_SESSION[SESSION_NAME]['uid'] = $user->id;

		do_redirect('index');
		exit;
	}

	echo 'No good.';
	exit;
}

$error = @$_GET['error'];

?>

<? if ($error): ?>
	<p class="error">Error: <?= html($error) ?></p>
<? endif ?>

<form method="post" action>
	<p>E-mail: <input type="email" name="user" value="<?= @$_COOKIE['lt_user'] ?>" required /></p>
	<p>Password: <input type="password" name="pass" required /></p>
	<p><input type="submit" value="Log in" /></p>
</form>
