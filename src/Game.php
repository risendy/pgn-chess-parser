<?php
namespace PgnParser;


use PgnParser\Move;
use PgnParser\Tag;

class Game {
	const TAGS = ['Event', 'Site', 'Date', 'Round', 'White', 'Black', 'Result', 'Annotator', 'PlyCount', 'TimeControl', 'Time', 'Termination', 'Mode', 'FEN'];

	const HEADER_REGEX = '/^(\[((?:\r?\n)|.)*\])(?:\r?\n){2}/';
	const HEADER_KEY_REGEX = '/^\[([A-Z][A-Za-z]*)\s.*\]$/';
	const HEADER_VALUE_REGEX = '/^\[[A-Za-z]+\s"(.*)"\]$/';
	const COMMENTS_REGEX = '/(\{[^}]+\})+?/';
	const MOVE_VARIATIONS_REGEX = '/(\([^\(\)]+\))+?/';
	const MOVE_NUMBER_REGEX = '/\d+\.(\.\.)?/';
	const MOVE_INDICATOR_REGEX = '/\.\.\./';
	const ANNOTATION_GLYPHS_REGEX = '/\$\d+/';
	const MULTIPLE_SPACES_REGEX = '/\s+/';

	private $stringMovesArray = [];
	private $objectMovesArray = []; 
	private $objectTagsArray = [];
	private $headerStr = '';

	private function extractTagsRegex($pgn) {
		$regex =  preg_match(self::HEADER_REGEX, $pgn, $matches);

		return $matches[1];
	}

	private function deleteComments() {
		$this->moveText = preg_replace(self::COMMENTS_REGEX, '', $this->moveText);
	}

	private function deleteMoveVariations() {
		$this->moveText = preg_replace(self::MOVE_VARIATIONS_REGEX, '', $this->moveText);
	}

	private function deleteMoveNumber() {
		$this->moveText = preg_replace(self::MOVE_NUMBER_REGEX, '', $this->moveText);	
	}

	private function deleteAnnotationGlyphs() {
		$this->moveText = preg_replace(self::ANNOTATION_GLYPHS_REGEX, '', $this->moveText);	
	}

	private function deleteMultipleSpaces() {
		$this->moveText = preg_replace(self::MULTIPLE_SPACES_REGEX, '', $this->moveText);	
	}

	private function trimMoveStr() {
		$this->moveText = trim($this->moveText);

		$this->moveText = preg_replace(self::MULTIPLE_SPACES_REGEX, ' ', $this->moveText);
	}

	private function clearMoveStr() {
		$this->deleteComments();
		$this->deleteMoveVariations();
		$this->deleteMoveNumber();
		$this->deleteAnnotationGlyphs();
		$this->trimMoveStr();
	}

	private function extractMovesStr() {
		$this->moveText = str_replace($this->headerStr, '', $this->pgn);
	}

	private function createObjectHeaderArray() {
		$headerElementsArray = explode(PHP_EOL, $this->headerStr);
		$headerElementsArray = $this->removeEmptyArrayElements($headerElementsArray);
		$headerElementsArray = $this->trimArrayElements($headerElementsArray);

		if ($headerElementsArray) {
			for ($i=0; $i < sizeof($headerElementsArray); $i++) { 
				preg_match(self::HEADER_KEY_REGEX, $headerElementsArray[$i], $matchesKey);
				preg_match(self::HEADER_VALUE_REGEX, $headerElementsArray[$i], $matchesValue);

				$tag = new Tag($headerElementsArray[$i], $matchesKey[1], $matchesValue[1]);

				$this->objectTagsArray[$matchesKey[1]] = $tag;
			}
		}
	}

	private function trimArrayElements($array) {
		return array_map('trim', $array); 
	}

	private function removeEmptyArrayElements($array) {
		return array_filter($array);
	}

	private function createSimpleMovesArray() {
		$this->stringMovesArray = explode(' ', $this->moveText);
	}

	private function createObjectMovesArray() {
		if ($this->stringMovesArray) {
			$moveCounter = 1;

			for ($i = 0; $i < sizeof($this->stringMovesArray); $i++) {
				//white
				if ($i % 2 == 0) {
					$move = new Move($this->stringMovesArray[$i], $moveCounter, 'W');

					$this->objectMovesArray[$moveCounter][] = $move;	
				}
				//black
				else
				{
					$move = new Move($this->stringMovesArray[$i], $moveCounter, 'B');
					$this->objectMovesArray[$moveCounter][] = $move;

					$moveCounter++;
				}
			}
		}
	}

	public function parsePgn($pgn){
	    $this->pgn = $pgn;

		$header = $this->extractTagsRegex($pgn);

		if ($header) {
			$this->headerStr = $header;
			$this->createObjectHeaderArray();

			$this->extractMovesStr();
			$this->clearMoveStr();
			$this->createSimpleMovesArray();
			$this->createObjectMovesArray();
 		}
 		else
 		{
 			$this->headerStr = NULL;
 		}
	}

	public function getTagValueByName($tagKey) {
		if (!isset($this->objectTagsArray[$tagKey])){
			throw new \Exception("Non existent tag name", 1);
		}

		return $this->objectTagsArray[$tagKey]->getValue();
	}

	public function getMove($moveNumber, $color = 'W') {
		if ($color == 'W') {$index = 0;} else {$index = 1;}

		if (!isset($this->objectMovesArray[$moveNumber])) {
			throw new Exception("Non existent move number", 1);
		}

		return $this->objectMovesArray[$moveNumber][$index]->getMove();
	}

	public function getFirstMove($color = 'W') {
		if ($color == 'W') {$index = 0;} else {$index = 1;}

		if (!isset($this->objectMovesArray[1][$index])){
			throw new \Exception("Non existent move number", 1);
		}

		return $this->objectMovesArray[1][$index]->getMove();
	}

	public function getLastMove($color = 'W') {
		if ($color == 'W') {$index = 0;} else {$index = 1;}

		if (!isset($this->objectMovesArray[sizeof($this->objectMovesArray)][$index])) {
			throw new \Exception("Non existent move number", 1);
		}

		return $this->objectMovesArray[sizeof($this->objectMovesArray)][$index]->getMove();
	}

	public function getSimpleMovesArray() {
		return $this->stringMovesArray;
	}

	public function getObjectMovesArray() {
		return $this->objectMovesArray;
	}

    public function getMovesString() {
        return $this->moveText;
    }

    public function getHeaderString() {
        return $this->headerStr;
    }
}