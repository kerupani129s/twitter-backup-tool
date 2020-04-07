<?php

require_once(__DIR__ . '/../inc/twitter.php');
require_once(__DIR__ . '/../inc/util.php');
require_once(__DIR__ . '/../inc/util-user.php');

$pathInfo = explode('/', $_SERVER['PATH_INFO']);

$login = $pathInfo[1];
$screenName = $pathInfo[2];

// 
$twitter = new Twitter($login);

$cursor = '-1';

$users = [];

do {

	// 
	$result = $twitter->get('friends/list', [
		'screen_name' => $screenName,
		'skip_status' => 'true',
		'count' => '100',
		'cursor' => $cursor
	]);

	foreach ($result->users as $user) {
		$users[] = $user;
	}

	// 
	$cursor = $result->next_cursor_str;

} while ( $cursor !== '0' );

// 
echo '<div>Total Count: ' . count($users) . '</div><br>';

foreach ($users as $user) {
	echo '<div>';
	printUser($user);
	echo '</div><br>';
}
