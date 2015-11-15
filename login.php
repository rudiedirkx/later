<?php

require 'inc.bootstrap.php';

if ( is_logged_in(false) ) {
	do_redirect('index');
	echo "You're already logged in....";
	exit;
}

if ( isset($_POST['user'], $_POST['pass']) ) {
	setcookie('lt_user', $_POST['user']);

	// Both fields are required.
	if ( !trim($_POST['user']) || !trim($_POST['pass']) ) {
		echo '<em>E-mail</em> and <em>Password</em> must not be empty.';
		exit;
	}

	// Create new account.
	if ( !empty($_POST['create']) ) {
		// Check e-mail.
		if ( !filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) ) {
			echo '<em>E-mail</em> must be valid e-mail.';
			exit;
		}

		// Check password.
		if ( strlen(trim($_POST['pass'])) < 6 ) {
			echo '<em>Password</em> must be at least 6 characters.';
			exit;
		}

		// Check e-mail existence.
		if ( $db->select('users', array('email' => $_POST['user']))->first() ) {
			echo '<em>E-mail</em> already exists in this db.';
			exit;
		}

		// Create account!
		$db->insert('users', array(
			'email' => $_POST['user'],
		));
		$id = $db->insert_id();
		$db->update('users', array(
			'password' => sha1($id . ':' . $_POST['pass']),
		), compact('id'));
	}

	$args = array($_POST['user'], ':', $_POST['pass']);
	$user = $db
		->select('users', 'email = ? AND password = SHA1(CONCAT(id, ?, ?))', $args)
		->first();

	if ( $user ) {
		@session_start();

		$_SESSION[SESSION_NAME]['uid'] = $user->id;
		$_SESSION[SESSION_NAME]['salt'] = rand();

		do_redirect('index');
		exit;
	}

	echo 'Invalid credentials.';
	exit;
}

$error = @$_GET['error'];

require 'tpl.header.php';

?>

<style>
form.create-account button .log-in,
form:not(.create-account) button .create {
	display: none;
}
</style>

<? if ($error): ?>
	<p class="error">Error: <?= html($error) ?></p>
<? endif ?>

<form method="post" action>
	<p class="form-item">E-mail: <input type="email" name="user" value="<?= @$_COOKIE['lt_user'] ?>" required autofocus /></p>
	<p class="form-item">Password: <input type="password" name="pass" required /></p>
	<p><label><input type="checkbox" name="create" /> Create account</label></p>
	<p>
		<button><span class="log-in">Log in</span><span class="create">Create account</span></button>
	</p>
</form>

<script>
document.querySelector('input[name="create"]').addEventListener('change', function(e) {
	document.querySelector('form').classList.toggle('create-account', this.checked);
});
</script>
