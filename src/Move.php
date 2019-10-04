<?php
namespace PgnParser;


class Move {
	private $move;
	private $moveNumber;
	private $moveColor;

	function __construct($move, $moveNumber, $moveColor) {
		$this->move = $move;
		$this->moveNumber = $moveNumber;
		$this->moveColor = $moveColor;
	}

	public function getMove() {
		return $this->move;
	}
}