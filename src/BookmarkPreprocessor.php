<?php

namespace rdx\later;

interface BookmarkPreprocessor {

	public function beforeMatch( array &$data );

	public function beforeSave( array &$data );

}
