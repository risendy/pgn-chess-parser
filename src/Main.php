<?php
namespace PgnParser;

use PgnParser\Game;

require '../vendor/autoload.php';

$game = new Game();
$game->parsePgn();

var_dump($game->getMoves());
