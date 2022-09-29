<?php

namespace rdx\later;

class ExactMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int {
		global $db, $user;

		if ($id = $db->select_one('urls', 'id', ['user_id' => $user->id, 'url' => $data['url']])) {
			return $id;
		}

		return null;
	}

}
