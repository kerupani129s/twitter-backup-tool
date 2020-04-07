<?php

require_once(__DIR__ . '/../inc/twitter.php');
require_once(__DIR__ . '/../inc/util.php');
require_once(__DIR__ . '/../inc/util-tweet.php');
require_once(__DIR__ . '/../inc/util-tweet-media.php');

$pathInfo = explode('/', $_SERVER['PATH_INFO']);

$login = $pathInfo[1];
$id = $pathInfo[2];

// 
$twitter = new Twitter($login);

$tweet = $twitter->get('statuses/show', ['id' => $id, 'tweet_mode' => 'extended']);

// 
echo '<div>';
printTweet($tweet);
printTweetMedia($tweet);
echo '</div><br>';

echo '<pre>';
echo var_dump($tweet);
echo '</pre>';
