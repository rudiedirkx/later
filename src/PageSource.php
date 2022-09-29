<?php

namespace rdx\later;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Cookie\CookieJar;
use rdx\jsdom\Node;

class PageSource {

	public ?string $html = null;
	public ?Node $dom = null;

	public function __construct(public string $url) {}

	public function fetch() : self {
		if ($this->dom) return $this;

		$guzzle = new Guzzle([
			'cookies' => $cookies = new CookieJar(),
			'headers' => ['User-agent' => 'WhatsApp/2.20.108 A'],
			// 'allow_redirects' => [
			// 	'track_redirects' => true,
			// ] + RedirectMiddleware::$defaultSettings,
		]);
		$rsp = $guzzle->get($this->url);

		$this->html = (string) $rsp->getBody();

		$charset = null;
		if (count($contentType = $rsp->getHeader('Content-type'))) {
			if (preg_match('#charset\s*=\s*([^;]+)#', $contentType[0], $match)) {
				$charset = $match[1];
			}
		}

		$this->dom = Node::create($this->html, $charset);

		return $this;
	}

	static public function fromUrl(string $url) {
		return (new static($url))->fetch();
	}

}
