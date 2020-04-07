<?php

require_once(__DIR__ . '/../inc/twitter.php');
require_once(__DIR__ . '/../inc/util.php');
require_once(__DIR__ . '/../inc/util-tweet.php');
require_once(__DIR__ . '/../inc/util-tweet-media.php');

$pathInfo = explode('/', $_SERVER['PATH_INFO']);

$login = $pathInfo[1];

// 
$twitter = new Twitter($login);

// 
$tweets = json_decode(@file_get_contents(__DIR__ . '/tweets.json'));

// 動画が Blob だったものを API から取得
foreach ($tweets as &$tweet) {

	if ( ! property_exists($tweet->entities, 'media') ) continue;

	// 
	$media = $tweet->extended_entities->media[0];

	if ( $media->type !== 'video' && $media->type !== 'animated_gif' ) continue;

	$variant = $media->video_info->variants[0];

	if ( $variant->url !== '#dummy' ) continue;

	// 
	$tweet = $twitter->get('statuses/show', ['id' => $tweet->id_str, 'tweet_mode' => 'extended']);

}
unset($tweet);

// 返信ツイートを API から取得 (オプショナル)
foreach ($tweets as &$tweet) {

	if ( $tweet->in_reply_to_status_id_str !== '#dummy' ) continue;

	// 
	$tweet = $twitter->get('statuses/show', ['id' => $tweet->id_str, 'tweet_mode' => 'extended']);

}
unset($tweet);

// 
echo '<style> span > div { display: inline-block; } span > div > img { height: 100%; } </style>';

echo '<div>Total Count: ' . count($tweets) . '</div><br>';

foreach ($tweets as $tweet) {
	echo '<div>';
	printTweet($tweet);
	printTweetMedia($tweet);
	echo '</div><br>';
}
