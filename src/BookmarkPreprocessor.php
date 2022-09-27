<?php

namespace rdx\later;

interface BookmarkPreprocessor {

	public function beforeMatch( array &$data, ?string $html = null );

	public function beforeSave( array &$data, ?string $html = null );

}
