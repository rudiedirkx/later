<?php

namespace rdx\later;

class PreprocessWhitespace implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, PageSource $source ) : void {
		$this->preprocess($data);
	}

	public function beforeSave( array &$data, PageSource $source ) : void {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) : void {
		$data['title'] = trim($data['title']);
		$data['url'] = trim($data['url']);
	}

}
