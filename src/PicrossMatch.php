<?php

namespace rdx\later;

class PicrossMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) {
		global $db, $user;

		if ( preg_match('#^https?://.*?games\..+?/119.php#', $data['url'], $match) ) {
			$url = $match[0];
			return
				$db->select_one('urls', 'id', "archive = 0 AND url LIKE ?", "$url%") ?:
				$db->select_one('urls', 'id', "url LIKE ?", "$url%");
		}
	}

}
