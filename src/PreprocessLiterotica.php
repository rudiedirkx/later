<?php

namespace rdx\later;

use rdx\jsdom\Node;

class PreprocessLiterotica implements BookmarkPreprocessor {

	public function beforeMatch( array &$data, PageSource $source ) : void {
	}

	public function beforeSave( array &$data, PageSource $source ) : void {
		$origHost = parse_url($data['url'], PHP_URL_HOST);
		$domain = preg_replace('#^(www|m|i)\.#', '', $origHost);

		if ($domain != 'literotica.com') {
			return;
		}

		$title = trim(preg_replace('# \([^\)]*\)$#', '', $data['title']));

		$newUrl = str_replace("//$origHost", "//m.$domain", $data['url']);
		$checkUrl = str_replace("//$origHost", "//www.$domain", $data['url']);

		$dom = (new PageSource($checkUrl))->fetch()->dom;
		$tagLinks = $dom->queryAll('.panel a[href*="//tags.literotica.com/"]');
		if (!count($tagLinks)) {
			$pageLinks = $dom->queryAll('.panel a[href*="?page="]');
			$pages = array_map(function(Node $el) : int {
				return preg_match('#\?page=(\d+)#', $el['href'], $match) ? (int) $match[1] : 0;
			}, $pageLinks);
			$lastPage = max($pages);
			$checkUrl = explode('?', $checkUrl)[0] . '?page=' . $lastPage;

			$dom = (new PageSource($checkUrl))->fetch()->dom;
			$tagLinks = $dom->queryAll('.panel a[href*="//tags.literotica.com/"]');
		}

		$tags = array_map(fn($el) => $el->textContent, $tagLinks);

		$data['title'] = $title . ' (' . implode(', ', $tags) . ')';
		$data['url'] = $newUrl;
	}

}
