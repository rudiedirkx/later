<?php

namespace rdx\later;

class PinggyMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		if ( $this->isPinggy($data['url']) ) {
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE '%.pinggy.link%' ORDER BY id DESC", [$user->id]);
			foreach ( $bookmarks as $id => $url ) {
				if ( $this->isPinggy($url) ) {
					return $id;
				}
			}
		}

		return null;
	}

	protected function isPinggy( string $url ) : bool {
		$hostname = parse_url($url, PHP_URL_HOST);
		return preg_match('#\.pinggy\.link$#', $hostname) > 0;
	}

}
