<?php

namespace rdx\later;

use rdx\jsdom\Node;

class PreprocessCorrespondent implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, PageSource $source ) : void {
	}

	public function beforeSave( array &$data, PageSource $source ) : void {
		$this->preprocess($data, $source);
	}

	protected function preprocess( array &$data, PageSource $source ) : void {
		$url = $data['url'];
		if ( !preg_match('#\b(de|the)correspondent\.(nl|com)#', $url) ) {
			return;
		}

		$pattern = '#- (De|The) Correspondent$#i';
		if ( !preg_match($pattern, $data['title']) ) {
			return;
		}

		$source->fetch();

		$timeEl = $source->dom->query('.publication-metadata__readingtime');
		if ( !$timeEl ) {
			return;
		}

		$titleEl = $source->dom->query('title');
		$title = trim(preg_replace($pattern, '', $titleEl->textContent));

		$time = trim($timeEl->textContent, ')(');
		$time = trim(preg_replace('#(leestijd|reading time|luistertijd|listening time)#i', '', $time));
		$time = preg_replace('#(minutes|minuten)#', 'min', $time);

		$data['title'] = "$title ($time)";
	}

}
