<?php
namespace PgnParser;

use PgnParser\Game;

require '../vendor/autoload.php';

$game = new Game();
$game->parsePgn();

//var_dump($game->getMove(5, 'B'));
var_dump($game->getLastMove('B'));