<?php

namespace rdx\later;

use rdx\jsdom\Node;

class PreprocessCorrespondent implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, ?string $html = null ) {
	}

	public function beforeSave( array &$data, ?string $html = null ) {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) {
		$url = $data['url'];
		if ( !preg_match('#\b(de|the)correspondent\.(nl|com)#', $url) ) {
			return;
		}

		$pattern = '#- (De|The) Correspondent$#';
		if ( !preg_match($pattern, $data['title']) ) {
			return;
		}

		$html = get_html($url);
		$dom = Node::create($html);

		$timeEl = $dom->query('.publication-metadata__readingtime');
		if ( !$timeEl ) {
			return;
		}

		$titleEl = $dom->query('title');
		$title = trim(preg_replace($pattern, '', $titleEl->textContent));

		$time = trim($timeEl->textContent, ')(');
		$time = trim(preg_replace('#(leestijd|reading time|luistertijd|listening time)#i', '', $time));
		$time = preg_replace('#(minutes|minuten)#', 'min', $time);

		$data['title'] = "$title ($time)";
	}

}
