javascript:
	(document.head||document.documentElement).appendChild((function(el) {
		var div = document.createElement('div');
		div.className = 'later-loading';
		div.textContent = '. . .';
		div.setAttribute('style', 'z-index: 2000999998; position: fixed; left: 20px; top: 50px; border: solid 20px black; padding: 10px 20px; background: white; color: black; font-size: 30px;');
		document.body.insertBefore(div, document.body.firstElementChild);
		el.src = '__BASE__?url=' + encodeURIComponent(location.href) + '&title=' + encodeURIComponent(document.title);
		return el;
	})((document.createElement||Document.prototype.createElement).call(document, 'script')));
	void(0);
