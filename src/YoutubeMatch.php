<?php

namespace rdx\later;

class YoutubeMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) {
		global $db, $user;

		if ( $vid = $this->getVideoId($data['url']) ) {
			$bookmarks = $db->select_fields('urls', 'id, url', "user_id = ? AND url LIKE ?", [$user->id, "%$vid%"]);
			foreach ($bookmarks as $id => $url) {
				if ( $this->getVideoId($url) === $vid ) {
					return $id;
				}
			}
		}
	}

	protected function getVideoId( $url ) {
		$regexes = [
			'#//(?:www\.)?youtube.com/watch.*?[\?\&]v=([^&\#]+)#',
			'#//(?:www\.)?youtu\.be/([^&\#\?]+)#',
		];
		foreach ( $regexes as $regex ) {
			if ( preg_match($regex, $url, $match) ) {
				return $match[1];
			}
		}
	}

}
