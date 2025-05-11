<?php

namespace rdx\later;

class LiteroticaMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		$url = parse_url($data['url']);
		$domain = preg_replace('#^(www|m|i)\.#', '', $url['host']);
		if ($domain != 'literotica.com') {
			return null;
		}

		$match = 'literotica.com' . $url['path'];

		if ( $id = $db->select_one('urls', 'id', "user_id = ? AND url LIKE ? ORDER BY archive", [$user->id, "%$match%"]) ) {
			return $id;
		}

		return null;
	}

}
