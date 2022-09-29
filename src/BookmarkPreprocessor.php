<?php

namespace rdx\later;

interface BookmarkPreprocessor {

	public function beforeMatch( array &$data, PageSource $source ) : void;

	public function beforeSave( array &$data, PageSource $source ) : void;

}
