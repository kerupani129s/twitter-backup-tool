<?php

function printTweetMedia($tweet) {

	if ( property_exists($tweet->entities, 'media') ) {
		printMedia($tweet->extended_entities->media);
	}

}

function printMedia($medias) {

	foreach ($medias as $media) {

		$type = $media->type;

		echo '(' . $type . ')<br>';

		if ( $type === 'photo' ) {
			echo '<img style="max-width: 360px;" src="' . getImageUrlLarge($media->media_url_https) . '">';
		} else if ( $type === 'video' || $type === 'animated_gif' ) {
			echo '<video style="max-width: 360px;" controls preload="metadata" src="' . getVideoUrlLargeMp4($media) . '"></video>';
		}

		echo '<br>';

	}

}

function getImageUrlLarge($default) {
	return $default; // メモ: 2020/02/22 現在はそのままの URL
}

function getVideoUrlLargeMp4($media) {

	$variantsMp4 = array_filter($media->video_info->variants, function ($variant) {
		return $variant->content_type === 'video/mp4';
	});

	usort($variantsMp4, function ($a, $b) {
		return $b->bitrate - $a->bitrate;
	});

	return $variantsMp4[0]->url;

}
