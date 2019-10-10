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

## Usage
To parse pgn string:
```
$game = new Game();
$game->parsePgn($pgn);
```
To get moves in string format:
```
$moves = $game->getMovesString();

result:
string(15) "e4 e6 d4 d5 0-1"
```

To get moves in array format:
```
$moves = $game->getSimpleMovesArray();

result:
array(5) {
  [0]=>
  string(2) "e4"
  [1]=>
  string(2) "e6"
  [2]=>
  string(2) "d4"
  [3]=>
  string(2) "d5"
  [4]=>
  string(3) "0-1"
}
```
To get moves in object array format:
```
$moves = $game->getObjectMovesArray();

result:
array(3) {
  [1]=>
  array(2) {
    [0]=>
    object(PgnParser\Move)#22 (3) {
      ["move":"PgnParser\Move":private]=>
      string(2) "e4"
      ["moveNumber":"PgnParser\Move":private]=>
      int(1)
      ["moveColor":"PgnParser\Move":private]=>
      string(1) "W"
    }
    [1]=>
    object(PgnParser\Move)#23 (3) {
      ["move":"PgnParser\Move":private]=>
      string(2) "e6"
      ["moveNumber":"PgnParser\Move":private]=>
      int(1)
      ["moveColor":"PgnParser\Move":private]=>
      string(1) "B"
    }
  }
  [2]=>
  array(2) {
    [0]=>
    object(PgnParser\Move)#24 (3) {
      ["move":"PgnParser\Move":private]=>
      string(2) "d4"
      ["moveNumber":"PgnParser\Move":private]=>
      int(2)
      ["moveColor":"PgnParser\Move":private]=>
      string(1) "W"
    }
    [1]=>
    object(PgnParser\Move)#25 (3) {
      ["move":"PgnParser\Move":private]=>
      string(2) "d5"
      ["moveNumber":"PgnParser\Move":private]=>
      int(2)
      ["moveColor":"PgnParser\Move":private]=>
      string(1) "B"
    }
  }
  [3]=>
  array(1) {
    [0]=>
    object(PgnParser\Move)#26 (3) {
      ["move":"PgnParser\Move":private]=>
      string(3) "0-1"
      ["moveNumber":"PgnParser\Move":private]=>
      int(3)
      ["moveColor":"PgnParser\Move":private]=>
      string(1) "W"
    }
  }
}
```
To get specific move:
```
$move = $game->getMove(2, 'B');

result:
string(2) "d4"
```
To get tag value by name:
```
$tagValue = $game->getTagValueByName('Black');

result:
string(5) "bmbio"
```

## Tests
If you want to run tests use:
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/PgnParserTest
```