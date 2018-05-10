<?php

namespace rdx\later;

class ExactExceptHashMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) {
		global $db, $user;

		$url = $this->prepUrl($data['url']);
		$bookmarks = $db->select_fields('urls', 'id, url', 'url LIKE ?', ["$url%"]);

		foreach ( $bookmarks as $id => $checkUrl ) {
			if ( $this->prepUrl($checkUrl) == $url ) {
				return $id;
			}
		}
	}

	protected function prepUrl( $url ) {
		return preg_replace('/#.+$/', '', $url);
	}

}
