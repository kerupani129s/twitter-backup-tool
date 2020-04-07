<?php

function printUser($user) {

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

	$description = replaceEntitiesToLinks($user->description, $user->entities->description);
	echo nl2br($description, false) . '<br>';

	if ( $user->url ) {
		$url = replaceEntitiesToLinks($user->url, $user->entities->url);
		echo $url . '<br>';
	}

}
