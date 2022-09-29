<?php

namespace rdx\later;

interface BookmarkMatcher {

	public function findBookmarkId( array $data ) : ?int;

}
