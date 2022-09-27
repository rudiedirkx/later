<?php

namespace rdx\later;

class PreprocessWhitespace implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, ?string $html = null ) {
		$this->preprocess($data);
	}

	public function beforeSave( array &$data, ?string $html = null ) {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) {
		$data['title'] = trim($data['title']);
		$data['url'] = trim($data['url']);
	}

}
