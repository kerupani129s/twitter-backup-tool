const tweets = [];

(() => {

	const target = document.querySelector('div[aria-label^="タイムライン: "]');

	const parseTweet = node => {

		const user = {};

		// ツイート日時・ツイート ID
		const nodeTime = node.getElementsByTagName('time')[0];

		const created_at = nodeTime.getAttribute('datetime');

		const id_str = nodeTime.parentNode.getAttribute('href').match(/\/(\d+)$/)[1];

		// ツイート本文
		const nodeTweetHtml = node.children[1].children[1]
		.lastElementChild.previousElementSibling.previousElementSibling;

		const full_text = (
				nodeTweetHtml.children.length === 0 ?
				'' :
				nodeTweetHtml.children[0].innerHTML.replace(/(<[^<>]*href=")\//g, '$1https://twitter.com/')
				);

		// ツイート画像・動画・GIF
		const media = [];

		const nodeMedia = node.children[1].children[1]
		.lastElementChild.previousElementSibling;

		[...nodeMedia.getElementsByTagName('img')].forEach(node => {

			const media_url_https = node.src.replace(/\?(?:[^&]*&)*format=([^&]*)(?:&[^&]*)*$/, '.$1');

			media.push({media_url_https, type: 'photo'});

		});

		[...nodeMedia.getElementsByTagName('video')].forEach(node => {

			// TODO: 高画質版かどうか？
			const url = node.src;

			const type = ('GIF' === nodeMedia.textContent ? 'animated_gif' : 'video');

			if ( /^blob:/.test(url) ) {
				media.push({
					type,
					video_info: {
						variants: [{bitrate: 0, content_type: 'video/mp4', url: '#dummy'}]
					}
				});
			} else {
				media.push({
					type,
					video_info: {
						variants: [{bitrate: 0, content_type: 'video/mp4', url}]
					}
				});
			}

		});

		// 返信 (メンションは保留)
		let in_reply_to_status_id_str = null;
		let in_reply_to_screen_name = null;

		if ( 4 <= node.children[1].children[1].children.length ) {

			const nodeReplayingToUserLinks = node.children[1].children[1].children[0];

			const nodeReplayingToUserLink = nodeReplayingToUserLinks.getElementsByTagName('a')[0];

			in_reply_to_status_id_str = '#dummy';
			in_reply_to_screen_name = nodeReplayingToUserLink.getAttribute('href').match(/\/([^\/]+)$/)[1];

		}

		// ツイートユーザー名・ID
		const nodeUserLink = node.children[1].children[0].getElementsByTagName('a')[0];

		user.name = nodeUserLink.getElementsByTagName('span')[0].textContent;

		user.screen_name = nodeUserLink.getAttribute('href').match(/\/([^\/]+)$/)[1];

		// 認証済み・非公開
		const nodeSvgArray = [...node.children[1].children[0].getElementsByTagName('svg')];

		user.protected = nodeSvgArray.some(nodeSvg => '非公開アカウント' === nodeSvg.getAttribute('aria-label'));
		user.verified = nodeSvgArray.some(nodeSvg => '認証済みアカウント' === nodeSvg.getAttribute('aria-label'));

		// ツイートユーザープロフィール画像
		const nodeUserImg = node.children[0].getElementsByTagName('a')[0].getElementsByTagName('img')[0];

		user.profile_image_url_https = nodeUserImg.src.replace('_normal.', '.').replace('_bigger.', '.').replace('_mini.', '.');

		// 
		return {
			created_at,
			id_str,
			full_text,
			display_text_range: null,
			entities: (media.length === 0 ? {user_mentions: []} : {user_mentions: [], media: {}}),
			extended_entities: (media.length === 0 ? {} : {media}),
			in_reply_to_status_id_str,
			in_reply_to_screen_name,
			user
		};

	};

	const getTweets = () => {

		target.querySelectorAll('article div[data-testid="tweet"]').forEach(node => {

			const tweet = parseTweet(node);

			const i = tweets.findIndex(tweet2 => tweet2.id_str === tweet.id_str);

			if ( i === -1 ) {
				tweets.push(tweet);
			} else {
				tweets[i] = tweet;
			}

		});

	};

	const observer = new MutationObserver(mutations => {
		getTweets();
	})

	observer.observe(target, {subtree: true, childList: true});

	getTweets();

})();
