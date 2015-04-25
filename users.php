<?php

require 'inc.bootstrap.php';

is_logged_in(true);

$users = $db->fetch('
	SELECT u.email, COUNT(x.id) AS num
	FROM users u
	LEFT JOIN urls x ON (x.user_id = u.id)
	GROUP BY u.id
	ORDER BY u.email
')->all();

require 'tpl.header.php';

?>

<style>
table {
	border-collapse: collapse;
	box-shadow: 0 0 20px #ccc;
}
tr:nth-child(odd) {
	background-color: #f7f7f7;
}
td, th {
	padding: 4px 6px;
	border: solid 1px #999;
}
</style>

<table>
	<? foreach ($users as $user): ?>
		<tr>
			<td><?= html($user->email) ?></td>
			<td align="right"><?= html($user->num) ?> urls</td>
		</tr>
	<? endforeach ?>
</table>
