<?php

function printTweet($tweet) {

	if ( property_exists($tweet, 'retweeted_status') ) {
		printTweetReplayingToTweetLink($tweet->retweeted_status);
		printTweetRetweetedLink($tweet);
		printTweetMain($tweet->retweeted_status);
	} else {
		printTweetReplayingToTweetLink($tweet);
		printTweetMain($tweet);
	}

}

function printTweetReplayingToTweetLink($tweet) {

	if ( $tweet->in_reply_to_status_id_str ) {
		echo getTweetLink($tweet->in_reply_to_screen_name, $tweet->in_reply_to_status_id_str) . '<br>｜<br>';
	}

}

function printTweetRetweetedLink($tweet) {

	$user = $tweet->user;

	$screenName = $user->screen_name;
	$name = $user->name;
	$createdAt = $tweet->created_at;
	echo '&#x1f503; <a href="https://twitter.com/' . $screenName . '">' . $name . '</a> Retweeted at ' . $createdAt . '<br>';

}

function printTweetMain($tweet) {

	printTweetUser($tweet->user);

	echo getCreatedAtLink($tweet) . '<br>';

	printTweetReplayingToUserLink($tweet);

	$fullText = replaceEntitiesToLinks($tweet->full_text, $tweet->entities, $tweet->display_text_range);
	echo nl2br($fullText, false) . '<br>';

}

function printTweetUser($user) {

	$profileImageUrl = getProfileImageUrlOriginal($user->profile_image_url_https);
	echo '<img src="' . $profileImageUrl . '" style="width: 48px;"><br>';

	echo $user->name;

	if ( $user->verified ) {
		echo ' <span style="color: #fff; background-color: #08f;">&#x2714;</span>';
	}

	if ( $user->protected ) {
		echo ' <span style="filter: grayscale(100%); background-color: #000;">&#x1f512;</span>';
	}

	echo '<br>';

	$screenName = $user->screen_name;
	echo '<a href="https://twitter.com/' . $screenName . '">@' . $screenName . '</a><br>';

}

function getTweetLink($screenName, $statusIdStr) {
	$tweetUrl = 'https://twitter.com/' . $screenName . '/status/' . $statusIdStr;
	return '<a href="' . $tweetUrl . '">' . $tweetUrl . '</a>';
}

function getCreatedAtLink($tweet) {
	return '<a href="https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id_str . '">' . $tweet->created_at . '</a>';
}

function printTweetReplayingToUserLink($tweet) {

	if ( ! $tweet->in_reply_to_status_id_str ) return;

	$mentionScreenNames = array_map(function ($user) {
		return $user->screen_name;
	}, $tweet->entities->user_mentions);

	$replayingToScreenNames = array_filter($mentionScreenNames, function ($screenName) use ($tweet) {
		return $screenName !== $tweet->in_reply_to_screen_name;
	});

	array_unshift($replayingToScreenNames, $tweet->in_reply_to_screen_name);

	if ( count($replayingToScreenNames) === 1 && $replayingToScreenNames[0] === $tweet->user->screen_name ) return;

	$userLinks = array_map(function ($screenName) {
		return '<a href="https://twitter.com/' . $screenName . '">@' . $screenName . '</a>';
	}, $replayingToScreenNames);

	echo 'Replying to ' . implode($userLinks, ' ') . '<br>';

}
