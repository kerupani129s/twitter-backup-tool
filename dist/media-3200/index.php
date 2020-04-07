<?php

require_once(__DIR__ . '/../inc/twitter.php');
require_once(__DIR__ . '/../inc/util.php');
require_once(__DIR__ . '/../inc/util-tweet.php');
require_once(__DIR__ . '/../inc/util-tweet-media.php');

$pathInfo = explode('/', $_SERVER['PATH_INFO']);

$login = $pathInfo[1];
$screenName = $pathInfo[2];

// 
$twitter = new Twitter($login);

$params = [
	'screen_name' => $screenName,
	'count' => '200',
	'tweet_mode' => 'extended'
];

// 
$tweets = [];

$i = 0;

while (true) {

	// 
	$result = $twitter->get('statuses/user_timeline', $params);

	if ( count($result) === 0 ) break;
	if ( array_key_exists('max_id', $params) && count($result) === 1 ) break;

	foreach ($result as $tweet) {
		if ( ! array_key_exists('max_id', $params) || $params['max_id'] !== $tweet->id_str ) {

			// メディアツイート
			if ( property_exists($tweet->entities, 'media') ) {
				$tweets[] = $tweet;
			}

		}
	}

	// 
	$params['max_id'] = $tweet->id_str;

	$i++;

}

// 
echo '<div>Total Count: ' . count($tweets) . '</div><br>';

foreach ($tweets as $tweet) {
	echo '<div>';
	printTweet($tweet);
	printTweetMedia($tweet);
	echo '</div><br>';
}
