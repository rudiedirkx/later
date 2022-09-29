<?php

namespace rdx\later;

class YoutubeMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		if ( $vid = $this->getVideoId($data['url']) ) {
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE ? ORDER BY archive", [$user->id, "%$vid%"]);
			foreach ($bookmarks as $id => $url) {
				if ( $this->getVideoId($url) === $vid ) {
					return $id;
				}
			}
		}

		if ( $list = $this->getPlaylistId($data['url']) ) {
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE ? ORDER BY archive", [$user->id, "%$list%"]);
			foreach ($bookmarks as $id => $url) {
				if ( $this->getPlaylistId($url) === $list ) {
					return $id;
				}
			}
		}

		return null;
	}

	protected function getPlaylistId( $url ) {
		$regexes = [
			'#//(?:www\.)?youtube.com/watch.*?[\?\&]list=([^&\#]+)#',
		];
		foreach ( $regexes as $regex ) {
			if ( preg_match($regex, $url, $match) ) {
				return $match[1];
			}
		}
	}

	protected function getVideoId( $url ) {
		$regexes = [
			'#//(?:www\.|m\.)?youtube.com/watch.*?[\?\&]v=([^&\#]+)#',
			'#//(?:www\.)?youtu\.be/([^&\#\?]+)#',
		];
		foreach ( $regexes as $regex ) {
			if ( preg_match($regex, $url, $match) ) {
				return $match[1];
			}
		}
	}

}
