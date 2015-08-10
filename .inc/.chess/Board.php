<?php

require_once('./.inc/.chess/Pawn.php');
require_once('./.inc/.chess/Castle.php');
require_once('./.inc/.chess/Knight.php');
require_once('./.inc/.chess/Bishop.php');
require_once('./.inc/.chess/Queen.php');
require_once('./.inc/.chess/King.php');

class Board {
  private static $_classRef;

  /**
   * @var int
   */
  private $id;

  /**
   * @var int
   */
  private $fpl;

  /**
   * @var int
   */
  private $spl;

  /**
   * @var int
   */
  private $rplayer;

  /**
   * @var int
   */
  private $player = 0;

  /**
   * @var array
   */
  private $table = array();

  /**
   * @var int
   */
  private $turn = 0;

  /**
   * @var int
   */
  private $aturn = 0;

  /**
   * @var int
   */
  private $lrok = 3;

  /**
   * @var int
   */
  private $srok = 3;

  /**
   * @var int
   */
  private $smove = 0;

  /**
   * @var int
   */
  private $active = 1;

  private $attach = array(0 => false, 1 => false);
  /**
   * @var int
   */
  private $win = -1;

  /**
   * @var int
   */
  private $index = -1;

  public function __construct () {
    $i = 0;

    while (++$i < 9) {
      $this->table[$i] = array();
      $j = 0;
      while (++$j < 9) {
      }
    }

    self::$_classRef = array('', 'King', 'Queen', 'Castle', 'Knight', 'Bishop', 'Pawn');
  }

  /**
   * @return int
   */
  public function getId () {
    return $this->id;
  }

  /**
   * @return int
   */
  public function getFplId () {
    return $this->fpl;
  }

  /**
   * @return int
   */
  public function getSplId () {
    return $this->spl;
  }

  /**
   * @param int $index
   *
   * @return int
   */
  public function getPlId ($index) {
    if ($index % 2 == 0) {
      return $this->fpl;
    } else {
      return $this->spl;
    }
  }

  /**
   * @return int
   */
  public function getRPlId () {
    return $this->getPlId($this->rplayer);
  }

  /**
   * @return int
   */
  public function getRPlayer () {
    return $this->rplayer;
  }

  /**
   * @param int $rplayer
   */
  public function setRPlayer ($rplayer) {
    $this->rplayer = $rplayer;
  }

  /**
   * @return int
   */
  public function getPlayer () {
    return $this->player;
  }

  /**
   */
  public function swapPlayer () {
    $this->player = ($this->player + 1) % 2;
  }

  /**
   * @return int
   */
  public function getTurn () {
    return $this->turn;
  }

  /**
   */
  public function addTurn () {
    ++$this->turn;
  }

  /**
   * @return int
   */
  public function getATurn () {
    return $this->aturn;
  }

  /**
   */
  public function updateATurn () {
    $this->aturn = $this->turn;
  }

  /**
   * @return int
   */
  public function getLRok () {
    return $this->lrok;
  }

  /**
   * @param int $ow
   *
   * @return bool
   */
  public function isLRok ($ow) {
    if ($ow == 0) {
      return ($this->lrok & 0x01) > 0;
    } else {
      return ($this->lrok & 0x02) > 0;
    }
  }

  /**
   * @param int $ow
   */
  public function flushLRok ($ow) {
    if ($ow == 0) {
      $this->lrok &= 0x02;
    } else {
      $this->lrok &= 0x01;
    }
  }

  /**
   * @return int
   */
  public function getSRok () {
    return $this->srok;
  }

  /**
   * @param int $ow
   *
   * @return bool
   */
  public function isSRok ($ow) {
    if ($ow == 0) {
      return ($this->srok & 0x01) > 0;
    } else {
      return ($this->srok & 0x02) > 0;
    }
  }

  /**
   * @param int $ow
   */
  public function flushSRok ($ow) {
    if ($ow == 0) {
      $this->srok &= 0x02;
    } else {
      $this->srok &= 0x01;
    }
  }

  /**
   * @return int
   */
  public function getSMove () {
    return $this->smove;
  }

  /**
   * @param int $y
   */
  public function registerSmove ($y) {
    $this->smove = $y;
  }

  /**
   * @return bool
   */
  public function isActive () {
    return $this->active;
  }

  /**
   * @return bool
   */
  public function isAttached () {
    return $this->attach[$this->rplayer];
  }

  /**
   * @return int
   */
  public function getWin () {
    return $this->win;
  }

  /**
   * @param  Figure $figure
   * @param  int $tx
   * @param  int $ty
   *
   * @return Figure
   */
  public function moveFigure ($figure, $tx, $ty) {
    $ret = null;

    if ($this->isCell($tx, $ty)) {
      $ret = $this->table[$tx][$ty];
    }

    $this->table[$tx][$ty] = $figure;
    $this->table[$figure->getX()][$figure->getY()] = null;
    $figure->setXY($tx, $ty);

    return $ret;
  }

  /**
   * @param  string $class
   * @param  int $owner
   * @param  int $x
   * @param  int $y
   *
   * @return Figure
   */
  public function createFigure ($class, $owner, $x, $y) {
    $figure = new $class(++$this->index, $owner);
    $figure->setXY($x, $y);
    $this->table[$x][$y] = $figure;

    return $figure;
  }

  /**
   * @return void
   */
  public function initDefaultBoard () {
    $y = 0;
    while (++$y < 9) {
      $this->createFigure('Pawn', 0, 2, $y);
    }

    $this->createFigure('Castle', 0, 1, 1);
    $this->createFigure('Knight', 0, 1, 2);
    $this->createFigure('Bishop', 0, 1, 3);
    $this->createFigure('Queen', 0, 1, 4);
    $this->createFigure('King', 0, 1, 5);
    $this->createFigure('Bishop', 0, 1, 6);
    $this->createFigure('Knight', 0, 1, 7);
    $this->createFigure('Castle', 0, 1, 8);

    $y = 0;
    while (++$y < 9) {
      $this->createFigure('Pawn', 1, 7, $y);
    }

    $this->createFigure('Castle', 1, 8, 1);
    $this->createFigure('Knight', 1, 8, 2);
    $this->createFigure('Bishop', 1, 8, 3);
    $this->createFigure('Queen', 1, 8, 4);
    $this->createFigure('King', 1, 8, 5);
    $this->createFigure('Bishop', 1, 8, 6);
    $this->createFigure('Knight', 1, 8, 7);
    $this->createFigure('Castle', 1, 8, 8);
  }

  /**
   * @param  array $data
   * @return void
   */
  public function fromArray ($data) {
    $this->id = intval($data['id']);
    $this->fpl = intval($data['fpl']);
    $this->spl = intval($data['spl']);
    $this->player = intval($data['player']);
    $this->turn = intval($data['turn']);
    $this->aturn = intval($data['aturn']);
    $this->lrok = intval($data['lrok']);
    $this->srok = intval($data['srok']);
    $this->active = intval($data['active']);
    $this->attach[0] = intval($data['fatch']) == 1;
    $this->attach[1] = intval($data['satch']) == 1;
    $this->smove = intval($data['smove']);
    $this->win = intval($data['win']);

    $this->loadShortDump($data['dump']);
  }

  /**
   * @return string
   */
  public function dump () {
    $result = '';

    $i = 0;
    while (++$i < 9) {
      $j = 0;
      while (++$j < 9) {
        if (!isset($this->table[$i][$j])) {
          $result .= '00';
        } else {
          $cell = $this->table[$i][$j];
          $result .= chr(ord('A') + 6 * $cell->getOwner() + $cell->getType()) . chr(ord('A') + $cell->getId());
        }
      }
    }

    return $result;
  }

  /**
   * @return string
   */
  public function shortDump () {
    $result = '';

    $i = 0;
    while (++$i < 9) {
      $j = 0;
      while (++$j < 9) {
        if (!isset($this->table[$i][$j])) {
          $result .= '0';
        } else {
          $cell = $this->table[$i][$j];
          $result .= chr(ord('A') + 6 * $cell->getOwner() + $cell->getType());
        }
      }
    }

    return $result;
  }

  /**
   * @param  string $dump
   *
   * @return void
   */
  public function loadShortDump ($dump) {
    $this->index = -1;

    $this->table = array();
    $i = 0;
    while (++$i < 9) {
      $this->table[$i] = array();
    }

    $i = -1;
    while (++$i < 64) {
      $type = substr($dump, $i, 1);
      if ('0' != $type) {
        $type = ord($type) - ord('A');
        $owner = intval(($type - 1) / 6);
        $type -= $owner * 6;
        $class = self::$_classRef[$type];

        $x = intval($i / 8) + 1;
        $y = $i % 8 + 1;
        $this->createFigure($class, $owner, $x, $y);
      }
    }
  }

  /**
   * @return array
   */
  public function getTable () {
    return $this->table;
  }

  /**
   * @param int $i
   *
   * @return array
   */
  public function getRow ($i) {
    return $this->table[$i];
  }

  /**
   * @param int $i
   * @param int $j
   *
   * @return Figure
   */
  public function getCell ($i, $j) {
    return $this->table[$i][$j];
  }

  /**
   * @param int $i
   * @param int $j
   * @return bool
   */
  public function isCell ($i, $j) {
    return isset($this->table[$i][$j]);
  }

  /**
   * @param bool $shift
   *
   * @return array(Figure)
   */
  public function getRPFigures ($shift = false) {
    $result = array();

    foreach ($this->table as $row) {
      foreach ($row as $cell) {
        if (isset($cell) && (
            ($shift && ($cell->getOwner() != $this->rplayer)) || (!$shift && ($cell->getOwner() == $this->rplayer)))) {
          $result[] = $cell;
        }
      }
    }

    return $result;
  }
}


?>