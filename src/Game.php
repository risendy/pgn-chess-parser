<?php
namespace PgnParser;


use PgnParser\Move;
use PgnParser\Tag;
use PgnParser\Cleaner;
use PgnParser\Extractor;

class Game {
	private $stringMovesArray = [];
	private $objectMovesArray = []; 
	private $objectTagsArray = [];
	private $headerStr = '';
	private $moveText = '';
	private $moveTextWithComments = '';

	function __construct() {
		$this->cleaner = new Cleaner();
		$this->extractor = new Extractor();
	}

	public function parsePgn($pgn){
	    $this->pgn = $pgn;

		$this->headerStr = $this->extractor->extractTagsRegex($pgn);

		if ($this->headerStr) {
			$this->createObjectHeaderArray();

			$extractedMoveText = $this->extractor->extractMovesStr($this->headerStr, $this->pgn);

			$this->moveText = $this->cleaner->clearMoveStr($extractedMoveText);
			$this->moveTextWithComments = $this->cleaner->clearMoveStr($extractedMoveText, $comments = true);
			
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
				$tagKey = $this->extractor->extractTagKey($headerElementsArray[$i]);
				$tagValue = $this->extractor->extractTagValue($headerElementsArray[$i]);

				$tag = new Tag($headerElementsArray[$i], $tagKey, $tagValue);

				$this->objectTagsArray[$tagKey] = $tag;
			}
		}
	}

	private function createSimpleMovesArray() {
		$this->stringMovesArray = explode(' ', $this->moveText);
	}

	private function createObjectMovesArray() {
		$stringMovesWithComment = explode(' ', $this->moveTextWithComments); 

		if ($stringMovesWithComment) {
			$moveCounter = 1;

			for ($i = 0; $i < sizeof($stringMovesWithComment); $i++) {
				$comment = false;
				$isComment = $this->extractor->extractComment($stringMovesWithComment[$i]);

				if ($isComment) continue;

				if (isset($stringMovesWithComment[$i+1])){
					$comment = $this->extractor->extractComment($stringMovesWithComment[$i+1]);	
				}

				//white
				if ($i % 2 == 0) {
					$move = new Move($stringMovesWithComment[$i], $moveCounter, $comment, 'W');

					$this->objectMovesArray[$moveCounter][] = $move;	
				}
				//black
				else
				{
					$move = new Move($stringMovesWithComment[$i], $moveCounter, $comment, 'B');
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

		return $this->objectMovesArray[$moveNumber][$index];
	}

	public function getFirstMove($color = 'W') {
		if ($color == 'W') {$index = 0;} else {$index = 1;}

		if (!isset($this->objectMovesArray[1][$index])){
			throw new \Exception("Non existent move number", 1);
		}

		return $this->objectMovesArray[1][$index]->getSan();
	}

	public function getLastMove($color = 'W') {
		if ($color == 'W') {$index = 0;} else {$index = 1;}

		if (!isset($this->objectMovesArray[sizeof($this->objectMovesArray)][$index])) {
			throw new \Exception("Non existent move number", 1);
		}

		return $this->objectMovesArray[sizeof($this->objectMovesArray)][$index]->getSan();
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