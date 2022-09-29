<?php

namespace rdx\later;

class F95ThreadMatch implements BookmarkMatcher {

	const F95_URL = 'https://f95zone.to/threads/{name}.{id}/';

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		if ( $f95Id = $this->getThreadId($data['url']) ) {
			$domain = parse_url(self::F95_URL, PHP_URL_HOST);
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE ? AND url LIKE ? ORDER BY archive asc, id desc", [$user->id, "%$domain%", "%$f95Id%"]);
			foreach ( $bookmarks as $id => $url ) {
				if ( $this->getThreadId($url) === $f95Id ) {
					return $id;
				}
			}
		}

		return null;
	}

	protected function getThreadId( $url ) {
		$pattern = '#^' . strtr(preg_quote(self::F95_URL, '#'), [
			preg_quote('{name}') => '[^\/\.]+',
			preg_quote('{id}') => '(\d+)',
		]) . '#';
		if ( preg_match($pattern, $url, $match) ) {
			return $match[1];
		}
	}

}
