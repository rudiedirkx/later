<?php

namespace rdx\later;

class ExactExceptHashMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		$url = $this->prepUrl($data['url']);
		$bookmarks = $db->select_fields('urls', 'id, url', 'user_id = ? AND url LIKE ?', [$user->id, "$url%"]);

		foreach ( $bookmarks as $id => $checkUrl ) {
			if ( $this->prepUrl($checkUrl) == $url ) {
				return $id;
			}
		}

		return null;
	}

	protected function prepUrl( $url ) {
		return preg_replace('/#.+$/', '', $url);
	}

}
