<?php

namespace rdx\later;

class ExactMatch implements BookmarkMatcher {

	public function findBookmarkId( array $data ) {
		global $db, $user;

		return $db->select_one('urls', 'id', ['user_id' => $user->id, 'url' => $data['url']]);
	}

}
