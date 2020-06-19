<?php

const LATER_LIMIT = 25;

const LATER_READABILITY_PARSER_API_TOKEN = '';
const LATER_READABILITY_RESPONSE_CACHE = '/tmp'; // No trailing slash

const LATER_BOOKMARK_MATCHERS = [
	rdx\later\ExactMatch::class
];

const LATER_BOOKMARK_PREPROCESSORS = [
	rdx\later\PreprocessWhitespace::class
];
