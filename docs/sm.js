(() => {
	const d = new Date();
	const s = document.createElement('script');
	s.src = 'https://kerupani129s.github.io/twitter-backup-tool/bookmarklet-save-moment.js?'
		+ [d.getFullYear(), d.getMonth() + 1, d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds()]
		.map((x, i) => ('' + x).padStart(i ? 2 : 4, '0')).join('');
	document.head.appendChild(s);
})();
