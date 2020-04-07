<?php

/**
 * 文字列に含まれるエンティティ (URL・ユーザー・ハッシュタグ・キャッシュタグ) をリンクに置換
 */
function replaceEntitiesToLinks($text, $entities, $displayTextRange = null) {

	$replacements = [];

	if ( property_exists($entities, 'urls') ) {
		foreach ($entities->urls as $entity) {
			$replacements[] = [
				'indices' => $entity->indices,
				'replacement' => '<a href="' . $entity->expanded_url . '">' . $entity->display_url . '</a>'
			];
		}
	}

	if ( property_exists($entities, 'user_mentions') ) {
		foreach ($entities->user_mentions as $entity) {
			$replacements[] = [
				'indices' => $entity->indices,
				'replacement' => '<a href="https://twitter.com/' . $entity->screen_name . '">@' . $entity->screen_name . '</a>'
			];
		}
	}

	if ( property_exists($entities, 'hashtags') ) {
		foreach ($entities->hashtags as $entity) {
			$replacements[] = [
				'indices' => $entity->indices,
				'replacement' => '<a href="https://twitter.com/hashtag/' . $entity->text . '">#' . $entity->text . '</a>'
			];
		}
	}

	if ( property_exists($entities, 'symbols') ) {
		foreach ($entities->symbols as $entity) {
			$replacements[] = [
				'indices' => $entity->indices,
				'replacement' => '<a href="https://twitter.com/search?q=%24' . $entity->text . '">$' . $entity->text . '</a>'
			];
		}
	}

	if ( property_exists($entities, 'media') ) {
		foreach ($entities->media as $media) {
			$replacements[] = [
				'indices' => $media->indices,
				'replacement' => '<a href="' . $media->expanded_url . '">' . $media->display_url . '</a>'
			];
		}
	}

	// 
	if ( $displayTextRange ) {

		$replacements = array_filter($replacements, function ($replacement) use ($displayTextRange) {
			return $displayTextRange[0] <= $replacement['indices'][0] && $replacement['indices'][1] <= $displayTextRange[1];
		});

		$replacements[] = ['indices' => [0, $displayTextRange[0]], 'replacement' => ''];
		$replacements[] = ['indices' => [$displayTextRange[1], mb_strlen($text)], 'replacement' => ''];

	}

	usort($replacements, function ($a, $b) {
		return $b['indices'][0] - $a['indices'][0];
	});

	// 
	foreach ($replacements as $replacement) {
		$text = mb_substr($text, 0, $replacement['indices'][0]) . $replacement['replacement'] . mb_substr($text, $replacement['indices'][1]);
	}

	return $text;

}

/**
 * プロフィール画像 (オリジナル) 取得
 */
function getProfileImageUrlOriginal($normal) {
	return str_replace('_normal.', '.', $normal);
}
