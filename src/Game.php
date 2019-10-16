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
	private $gameResult;

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
			$lastColor = 'B';

			for ($i = 0; $i < sizeof($stringMovesWithComment); $i++) {
				$comment = false;
                $currentMove = $stringMovesWithComment[$i];

                if ($i == sizeof($stringMovesWithComment) - 1) {
                    $gameResult = Move::staticCheckIfGameResult($currentMove);

                    if ($gameResult) {
                        $this->setGameResult($gameResult);
                    }
                }

                if (isset($stringMovesWithComment[$i+1])){
                    $nextMove = $stringMovesWithComment[$i+1];
                    $comment = $this->extractor->extractComment($nextMove);
                }

				$isComment = $this->extractor->extractComment($currentMove);

				if ($isComment) {
				    continue;
                }

				//white
				if ($lastColor == 'B') {
					$move = new Move($stringMovesWithComment[$i], $moveCounter, $comment, 'W');

					$this->objectMovesArray[$moveCounter][] = $move;
					$lastColor = 'W';
				}
				//black
				else
				{
					$move = new Move($stringMovesWithComment[$i], $moveCounter, $comment, 'B');
					$this->objectMovesArray[$moveCounter][] = $move;

                    $lastColor = 'B';
					$moveCounter++;
				}
			}
		}
	}

    public function createJsonArray() {
        $jsonArray = [];

        foreach ($this->objectTagsArray as $tag) {
            $jsonArray['tags'][$tag->getKey()] = $tag->getValue();
        }

        foreach ($this->objectMovesArray as $move) {
            if ((isset($move[0])) ? $moveWhite = $move[0]->getSan() : $moveWhite = NULL);
            if ((isset($move[1])) ? $moveBlack = $move[1]->getSan() : $moveBlack = NULL);

            $jsonArray['moves'][] = [
                'moveNumber' => $move[0]->getMoveNumber(),
                'white' => $moveWhite,
                'black' => $moveBlack,
            ];
        }

        return json_encode($jsonArray);
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

    /**
     * @return mixed
     */
    public function getGameResult()
    {
        return $this->gameResult;
    }

    /**
     * @param mixed $gameResult
     */
    public function setGameResult($gameResult)
    {
        $this->gameResult = $gameResult;
    }
}