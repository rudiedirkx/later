<?php

namespace rdx\later;

interface BookmarkMatcher {

	/**
	 * @return int
	 */
	public function findBookmarkId( array $data );

}
