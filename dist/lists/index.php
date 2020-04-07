<?php

require_once(__DIR__ . '/../inc/twitter.php');
require_once(__DIR__ . '/../inc/util.php');
require_once(__DIR__ . '/../inc/util-list.php');
require_once(__DIR__ . '/../inc/util-user.php');

$pathInfo = explode('/', $_SERVER['PATH_INFO']);

$login = $pathInfo[1];
$screenName = $pathInfo[2];

// 
$twitter = new Twitter($login);

$cursor = '-1';

$lists = [];

do {

	// 
	$result = $twitter->get('lists/ownerships', [
		'screen_name' => $screenName,
		'count' => '100',
		'cursor' => $cursor
	]);

	foreach ($result->lists as $list) {
		$lists[] = $list;
	}

	// 
	$cursor = $result->next_cursor_str;

} while ( $cursor !== '0' );

// 
$membersArray = [];

foreach ($lists as $list) {

	$cursor = '-1';

	$members = [];

	do {

		// 
		$result = $twitter->get('lists/members', [
			'list_id' => $list->id_str,
			'skip_status' => 'true',
			'count' => '100',
			'cursor' => $cursor
		]);

		foreach ($result->users as $user) {
			$members[] = $user;
		}

		// 
		$cursor = $result->next_cursor_str;

	} while ( $cursor !== '0' );

	$membersArray[$list->full_name] = $members;

}

// 
echo '<div>Total Count: ' . count($lists) . '</div><br>';

foreach ($lists as $list) {

	echo '<div style="padding: 1em; outline: solid #888 3px;">';
	printList($list);
	echo '</div><br>';

	// 
	$members = $membersArray[$list->full_name];

	echo '<div>Total Count: ' . count($members) . '</div><br>';

	foreach ($members as $user) {
		echo '<div>';
		printUser($user);
		echo '</div><br>';
	}

}
