<?php

namespace rdx\later;

class PreprocessWhitespace implements BookmarkPreprocessor {

	public function beforeMatch( array &$data ) {
		$this->preprocess($data);
	}

	public function beforeSave( array &$data ) {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) {
		$data['title'] = trim($data['title']);
		$data['url'] = trim($data['url']);
	}

}
