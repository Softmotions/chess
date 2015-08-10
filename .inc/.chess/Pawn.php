<?php

require_once( "./.inc/.chess/Figure.php" );

class Pawn extends Figure {

  public function __construct( $id, $owner ) {
    parent::__construct( $id, $owner );

    $this->type = 6;
  }

  public function getAvailableMoves( Board $board ) {
    $result = array();

    $x = $this->getX();
    $y = $this->getY();
    $d = $this->owner == 0 ? 1 : -1;

    if (!$board->isCell( $x + $d, $y)) {
      $result[] = array( 'x' => $x + $d, 'y' => $y, 'type' => 'move' );
    }

    if ($x - $d == 1 || $x - $d == 8) {
      if (!$board->isCell( $x + $d, $y) && !$board->isCell( $x + 2 * $d, $y)) {
        $result[] = array( 'x' => $x + 2 * $d, 'y' => $y, 'type' => 'smove' );
      }
    }

    if ($board->isCell( $x + $d, $y - 1) && $board->getCell( $x + $d, $y - 1)->getOwner() != $this->owner) {
      $result[] = array( 'x' => $x + $d, 'y' => $y - 1, 'type' => 'kill' );
    }

    if ($board->isCell( $x + $d, $y + 1) && $board->getCell( $x + $d, $y + 1)->getOwner() != $this->owner) {
      $result[] = array( 'x' => $x + $d, 'y' => $y + 1, 'type' => 'kill' );
    }

    if ($board->getSMove() != 0 && ( ( $d == -1 && $x == 4) || ( $d == 1 && $x == 5 ) ) && ($board->getSMove() == $y - 1 || $board->getSMove() == $y + 1 ) ) {
      $result[] = array( 'x' => $x + $d, 'y' => $board->getSMove(), 'type' => 'skill' );
    }

    return $result;
  }
}