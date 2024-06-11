<?php

namespace rdx\later;

class ExposeMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		if ( $this->isExpose($data['url']) ) {
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE '%.sharedwithexpose.com%' ORDER BY id DESC", [$user->id]);
			foreach ( $bookmarks as $id => $url ) {
				if ( $this->isExpose($url) ) {
					return $id;
				}
			}
		}

		return null;
	}

	protected function isExpose( string $url ) : bool {
		$hostname = parse_url($url, PHP_URL_HOST);
		return preg_match('#\.sharedwithexpose\.com$#', $hostname) > 0;
	}

}
