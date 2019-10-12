<?php
namespace PgnParser;


class Move {
	private $san;
	private $moveNumber;
	private $moveColor;
	private $comment;

	function __construct($san, $moveNumber, $comment, $moveColor) {
		$this->san = $san;
		$this->moveNumber = $moveNumber;
		$this->comment = $comment;
		$this->moveColor = $moveColor;
	}

	public function getSan() {
		return $this->san;
	}

	public function getComment() {
		return $this->comment;
	}
}