<?php
namespace PgnParser;

class Game {
	const TAGS = ['Event', 'Site', 'Date', 'Round', 'White', 'Black', 'Result', 'Annotator', 'PlyCount', 'TimeControl', 'Time', 'Termination', 'Mode', 'FEN'];

	const PGN1 = '[Event "2.f"]
	[Site "Leningrad"]
	[Date "1974.??.??"]
	[Round "3"]
	[White "Karpov, Anatoly"]
	[Black "Spassky, Boris"]
	[Result "1-0"]
	[ECO "E91"]
	[WhiteElo "2700"]
	[BlackElo "2650"]
	[Annotator "JvR"]
	[PlyCount "109"]
	[EventDate "1974.??.??"]

	 1. d4 Nf6 2. c4 g6 3. Nc3 Bg7 4. e4 d6 5. Nf3 O-O 6. Be2 c5 7. O-O Bg4 $5 { Spassky chooses a sharp opening.} 8. d5 Nbd7 9. Bg5 a6 10. a4 Qc7 11. Qd2 Rae8 12. h3 Bxf3 13. Bxf3 e6 $5 14. b3 Kh8 15. Be3 Ng8 16. Be2 e5 $5 17. g4 Qd8 18. Kg2 Qh4 $5 {Black takes the initiative on the kingside.} 19. f3 ({ The tactical justification is} 19. Bg5 Bh6) 19... Bh6 $2 { Tal, Keres and Botvinnik condemn this provocative move} ({and prefer} 19... f5) 20. g5 Bg7 21. Bf2 Qf4 22. Be3 Qh4 23. Qe1 $1 Qxe1 24. Rfxe1 h6 25. h4 hxg5 $2 ({A defence line against an attack on the queenside creates} 25... Ra8 26. Reb1 Rfb8 27. b4 Bf8 28. bxc5 Nxc5) 26. hxg5 Ne7 27. a5 f6 28. Reb1 fxg5 29. b4 $1 Nf5 $5 30. Bxg5 $1 ({Keres analyses} 30. exf5 e4 31. Bd2 exf3+ 32. Bxf3 gxf5 { Black has counter-play.}) 30... Nd4 31. bxc5 Nxc5 32. Rb6 Bf6 33. Rh1+ $1 Kg7 34. Bh6+ Kg8 35. Bxf8 Rxf8 36. Rxd6 Kg7 37. Bd1 Be7 ({Tal mentions} 37... Bd8 38. Na4 Bc7 39. Nxc5 Bxd6 40. Nxb7 {and 41.c5. White wins.}) 38. Rb6 Bd8 39. Rb1 Rf7 40. Na4 Nd3 41. Nb6 g5 42. Nc8 Nc5 43. Nd6 Rd7 44. Nf5+ Nxf5 45. exf5 e4 46. fxe4 Nxe4 47. Ba4 Re7 48. Rbe1 Nc5 49. Rxe7+ Bxe7 50. Bc2 Bd8 51. Ra1 Kf6 52. d6 Nd7 53. Rb1 Ke5 54. Rd1 Kf4 55. Re1 1-0';

	const PGN2 = '[Event "Lets Play!"]
		[Site "Chess.com"]
		[Date "2018.12.04"]
		[Round "?"]
		[White "guilherme_1910"]
		[Black "bmbio"]
		[Result "0-1"]
		[TimeControl "1/259200:0"]

		1. e4 e6 2. d4 d5 0-1';

	const HEADER_REGEX = '/^(\[((?:\r?\n)|.)*\])(?:\r?\n){2}/';
	const COMMENTS_REGEX = '/(\{[^}]+\})+?/';
	const MOVE_VARIATIONS_REGEX = '/(\([^\(\)]+\))+?/';
	const MOVE_NUMBER_REGEX = '/\d+\.(\.\.)?/';
	const MOVE_INDICATOR_REGEX = '/\.\.\./';
	const ANNOTATION_GLYPHS_REGEX = '/\$\d+/';
	const MULTIPLE_SPACES_REGEX = '/\s+/';

	function __construct()
    {

    }

    private $event;
    private $site;
    private $round;
    private $white;
    private $black;
    private $result;
    private $annotator;
    private $plyCount;
    private $timeControl;
    private $time;
    private $termination;
    private $mode;
    private $fen;
    private $tagsStr = '';
    private $moveStr = '';
	private $moves = []; 
	private $tags = [];

	public function extractTagsRegex($pgn) {
		$regex =  preg_match(self::HEADER_REGEX, $pgn, $matches);

		return $matches;
	}

	public function deleteComments() {
		$this->moveText = preg_replace(self::COMMENTS_REGEX, '', $this->moveText);
	}

	public function deleteMoveVariations() {
		$this->moveText = preg_replace(self::MOVE_VARIATIONS_REGEX, '', $this->moveText);
	}

	public function deleteMoveNumber() {
		$this->moveText = preg_replace(self::MOVE_NUMBER_REGEX, '', $this->moveText);	
	}

	public function deleteAnnotationGlyphs() {
		$this->moveText = preg_replace(self::ANNOTATION_GLYPHS_REGEX, '', $this->moveText);	
	}

	public function deleteMultipleSpaces() {
		$this->moveText = preg_replace(self::MULTIPLE_SPACES_REGEX, '', $this->moveText);	
	}

	public function trimMoveStr() {
		$this->moveText = trim($this->moveText);

		$this->moveText = preg_replace(self::MULTIPLE_SPACES_REGEX, ' ', $this->moveText);
	}

	public function clearMoveStr() {
		$this->deleteComments();
		$this->deleteMoveVariations();
		$this->deleteMoveNumber();
		$this->deleteAnnotationGlyphs();
		$this->trimMoveStr();
	}

	public function getMoveText() {
		$this->moveText = str_replace($this->headerStr, '', self::PGN1);
	}

	public function splitMoveStr() {
		$this->moves = explode(' ', $this->moveText);
	}

	public function parsePgn(){
		//$result = $this->extractTags(self::PGN1, '[', ']');
		$header = $this->extractTagsRegex(self::PGN1);

		if ($header) {
			$this->headerStr = $header[0];
			$this->getMoveText();
			$this->clearMoveStr();
			$this->splitMoveStr();
 		}
 		else
 		{
 			$this->header = NULL;
 		}
	}

	public function getMoves() {
		return $this->moves;
	}
}