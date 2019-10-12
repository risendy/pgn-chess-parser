<?php
namespace PgnParser;


class Extractor {
	const HEADER_REGEX = '/^(\[((?:\r?\n)|.)*\])(?:\r?\n){2}/';
	const HEADER_KEY_REGEX = '/^\[([A-Z][A-Za-z]*)\s.*\]$/';
	const HEADER_VALUE_REGEX = '/^\[[A-Za-z]+\s"(.*)"\]$/';
	const COMMENTS_REGEX = '/(\{[^}]+\})+?/';
	const MOVE_VARIATIONS_REGEX = '/(\([^\(\)]+\))+?/';
	const MOVE_NUMBER_REGEX = '/\d+\.(\.\.)?/';
	const MOVE_INDICATOR_REGEX = '/\.\.\./';
	const ANNOTATION_GLYPHS_REGEX = '/\$\d+/';
	const MULTIPLE_SPACES_REGEX = '/\s+/';

	function __construct() {

	}

	public function extractTagsRegex($pgn) {
		$regex =  preg_match(self::HEADER_REGEX, $pgn, $matches);

		if (!$matches) return false;

		return $matches[1];
	}

	public function extractTagKey($tag) {
		preg_match(self::HEADER_KEY_REGEX, $tag, $matchesKey);
		
		if (!$matchesKey) return false;

		return $matchesKey[1];
	}

	public function extractTagValue($tag) {
		preg_match(self::HEADER_VALUE_REGEX, $tag, $matchesValue);
		
		if (!$matchesValue) return false;

		return $matchesValue[1];
	}

	public function extractMovesStr($headerStr, $pgn) {
		return str_replace($headerStr, '', $pgn);
	}


	public function extractComment($nextMove) {
		$regex =  preg_match(self::COMMENTS_REGEX, $nextMove, $matches);

		if ($matches) {
			return preg_replace(['/{/', '/}/'], '', $matches[1]);
		}

		return false;
	}
}