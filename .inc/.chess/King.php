<?php

require_once( "./.inc/.chess/Figure.php" );

class King extends Figure {

  public function __construct( $id, $owner ) {
    parent::__construct( $id, $owner );

    $this->type = 1;
  }

  public function getAvailableMoves( Board $board ) {
    $result = array();

    $x = $this->getX();
    $y = $this->getY();

    $i = -1;
    while (++$i < 9) {
      $rx = $x + intval( $i / 3 ) - 1;
      $ry = $y + $i % 3 - 1;

      if ($rx < 1 || $rx > 8 || $ry < 1 || $ry > 8) {
        continue;
      }

      if (!$board->isCell( $rx, $ry )) {
        $result[] = array( 'x' => $rx, 'y' => $ry, 'type' => 'move' );
      } else if ($board->getCell( $rx, $ry )->getOwner() != $this->owner) {
        $result[] = array( 'x' => $rx, 'y' => $ry, 'type' => 'kill' );
      }
    }

    if ($board->isLRok( $this->getOwner() )) {
      $lrok = true;
      $i = 5;
      while (--$i > 1) {
        if ($board->isCell( $x, $i )) {
          $lrok = false;
          break;
        }
      }

      if ($lrok) {
        $result[] = array( 'x' => $x, 'y' => 3, 'type' => 'lrok' );
      }
    }

    if ($board->isSRok( $this->getOwner() )) {
      $srok = true;
      $i = 5;
      while (++$i < 8) {
        if ($board->isCell($x, $i)) {
          $srok = false;
          break;
        }
      }

      if ($srok) {
        $result[] = array( 'x' => $x, 'y' => 7, 'type' => 'srok' );
      }
    }

    return $result;
  }
}