<?php
namespace PgnParser;


use PgnParser\Move;
use PgnParser\Tag;
use PgnParser\Cleaner;

class Game {
	private $stringMovesArray = [];
	private $objectMovesArray = []; 
	private $objectTagsArray = [];
	private $headerStr = '';
	private $moveText = '';

	function __construct() {
		$this->cleaner = new Cleaner();
	}

	public function parsePgn($pgn){
	    $this->pgn = $pgn;

		$this->headerStr = $this->cleaner->extractTagsRegex($pgn);

		if ($this->headerStr) {
			$this->createObjectHeaderArray();

			$extractedMoveText = $this->cleaner->extractMovesStr($this->headerStr, $this->pgn);

			$this->moveText = $this->cleaner->clearMoveStr($extractedMoveText);
			$this->createSimpleMovesArray();
			$this->createObjectMovesArray();
 		}
	}

	private function createObjectHeaderArray() {
		$headerElementsArray = explode(PHP_EOL, $this->headerStr);
		$headerElementsArray = $this->cleaner->removeEmptyArrayElements($headerElementsArray);
		$headerElementsArray = $this->cleaner->trimArrayElements($headerElementsArray);

		if ($headerElementsArray) {
			for ($i=0; $i < sizeof($headerElementsArray); $i++) { 
				$tagKey = $this->cleaner->extractTagKey($headerElementsArray[$i]);
				$tagValue = $this->cleaner->extractTagValue($headerElementsArray[$i]);

				$tag = new Tag($headerElementsArray[$i], $tagKey, $tagValue);

				$this->objectTagsArray[$tagKey] = $tag;
			}
		}
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