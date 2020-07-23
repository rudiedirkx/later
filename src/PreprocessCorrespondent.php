<?php

namespace rdx\later;

use rdx\jsdom\Node;

class PreprocessCorrespondent implements BookmarkPreprocessor {

	public function beforeMatch( array &$data ) {
	}

	public function beforeSave( array &$data ) {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) {
		$pattern = '#- (De|The) Correspondent$#';
		if ( !preg_match($pattern, $data['title']) ) {
			return;
		}

		$url = $data['url'];
		$html = get_html($url);
		$dom = Node::create($html);

		$titleEl = $dom->query('title');
		$title = trim(preg_replace($pattern, '', $titleEl->textContent));

		$timeEl = $dom->query('.publication-metadata__readingtime');
		if ( $timeEl ) {
			$time = trim($timeEl->textContent, ')(');
			$time = trim(preg_replace('#(leestijd|reading time|luistertijd|listening time)#i', '', $time));
			$time = preg_replace('#(minutes|minuten)#', 'min', $time);

			$title .= " ($time)";
		}

		$data['title'] = $title;
	}

}
