<?php

namespace rdx\later;

class PreprocessQuery implements BookmarkPreprocessor {

	protected $remove = [];

	public function __construct( array $remove ) {
		$this->remove = $remove;
	}

	public function beforeMatch( array &$data, ?string $html = null ) {
		$this->preprocess($data);
	}

	public function beforeSave( array &$data, ?string $html = null ) {
		$this->preprocess($data);
	}

	protected function preprocess( array &$data ) {
		$url = $data['url'];

		$url = preg_replace('/([\?&#])(' . implode('|', $this->remove) . ')=[^&#]+/', '$1', $url);
		$url = preg_replace_callback('/([\?&#]{2,})/', function($match) {
			if ( strpos($match[1], '#') !== false ) {
				return '#';
			}

			if ( strpos($match[1], '?') !== false ) {
				return '?';
			}

			return '&';
		}, $url);
		$url = rtrim($url, '?&#');

		$data['url'] = $url;
	}

}
