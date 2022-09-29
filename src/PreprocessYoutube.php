<?php

namespace rdx\later;

use rdx\jsdom\Node;

class PreprocessYoutube implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, PageSource $source ) : void {
	}

	public function beforeSave( array &$data, PageSource $source ) : void {
		$this->preprocess($data, $source);
	}

	protected function preprocess( array &$data, PageSource $source ) : void {
		$url = $data['url'];
		if ( !preg_match('#\b(youtube\.com|youtu\.be)/#', $url) ) {
			return;
		}

		$pattern = '#- youtube$#i';
		if ( !preg_match($pattern, $data['title']) ) {
			return;
		}

		$source->fetch();

		$ytData = $this->getMetaData($source->dom);
		if ( !$ytData ) return;

		$title = trim($ytData['videoDetails']['title'] ?? '');
		$seconds = $ytData['videoDetails']['lengthSeconds'] ?? 0;
		if ( !$title || !$seconds ) return;

		$time = $this->makeTime($seconds);
		$data['title'] = "$title ($time)";
	}

	protected function makeTime(int $s) : string {
		$h = floor($s / 3600);
		$m = str_pad(floor(($s - $h * 3600) / 60), 2, '0', STR_PAD_LEFT);
		$s = str_pad($s % 60, 2, '0', STR_PAD_LEFT);
		return "$h:$m:$s";
	}

	protected function getMetaData(Node $dom) : ?array {
		$scripts = $dom->queryAll('script');
		foreach ( $scripts as $script ) {
			if ( preg_match('#ytInitialPlayerResponse\s*=\s*({[^<]+})#', $script->textContent, $match) ) {
				$data = json_decode($match[1], true);
				if ( $data ) {
					return $data;
				}
			}
		}

		return null;
	}

}
