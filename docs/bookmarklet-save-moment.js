// 
// メモ: 画面を一番下までスクロールしてから以下を実行
// 

// tweets[]

(() => {

	// モーメント情報取得
	// 
	// メモ: 公式の JSON はモーメントの情報とツイートの一覧が分かれており、また、
	//       直感的な構造になっていないため、 ここでは独自の構造で保存
	const parseMomentHeader = nodeMomentHeader => {

		const moment = {};

		// 
		moment.id = window.location.pathname.match(/\/(\d+)\/?$/)[1];

		moment.title = nodeMomentHeader.children[1].textContent;

		if ( 2 <= nodeMomentHeader.children[0].children.length ) {
			moment.time_string = nodeMomentHeader.children[0].children[1].textContent;
		}

		if ( 3 <= nodeMomentHeader.children.length ) {
			moment.description = nodeMomentHeader.children[2].textContent;
		}

		// メモ: ユーザー情報は保留

		return moment;

	};

	const nodeMomentHeader = document.querySelector('div[data-testid="placementTracking"]').nextElementSibling;
	const moment = parseMomentHeader(nodeMomentHeader);
	moment.tweets = tweets;

	// JSON 保存
	const a = document.createElement('a');
	a.href = 'data:application/json,' + encodeURIComponent(JSON.stringify(moment, null, 4));
	a.download = 'moment.' + moment.id + '.json';
	a.click();

})();
