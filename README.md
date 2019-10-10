## Table of contents
* [General info](#general-info)
* [Features](#features)
* [Usage](#installation)
* [Tests](#screenshots)

## General info
Simple pgn chess notation parser.

## Features
* extracting moves in string/array/object array formats
* getting specific moves (black or white)
* getting first/last move
* extracting tags in string format
* getting tags value by name

##Usage
To parse pgn string:
```
$game = new Game();
$game->parsePgn($pgn);
```
To get moves in string format:
```
$moves = $game->getMovesString();
```
To get moves in array format:
```
$moves = $game->getSimpleMovesArray();
```
To get moves in object array format:
```
$moves = $game->getObjectMovesArray();
```
To get specific move:
```
$move = $game->getMove(2, 'B');
```
To get tag value by name:
```
$tagValue = $game->getTagValueByName('Black');
```

## Tests
If you want to run tests use:
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/PgnParserTest
```