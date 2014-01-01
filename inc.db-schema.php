<?php

return array(
	'users' => array(
		'id' => array('pk' => true),
		'email',
		'password',
	),
	'urls' => array(
		'id' => array('pk' => true),
		'user_id' => array('unsigned' => true),
		'title',
		'url',
		'created' => array('unsigned' => true),
		'o' => array('unsigned' => true),
		'favorite' => array('unsigned' => true, 'default' => 0),
		'archive' => array('unsigned' => true, 'default' => 0),
	),
);
