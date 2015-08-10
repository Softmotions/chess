<?php

require_once( "./.inc/.chess/Figure.php" );

class Castle extends Figure {

  public function __construct( $id, $owner ) {
    parent::__construct( $id, $owner );

    $this->type = 3;
  }

  public function getAvailableMoves( Board $board ) {
    $result = array();

    $x = $this->getX();
    $y = $this->getY();

    $tmp = array();
    $i = 1;
    while (++$i < 7) {
      $rx = intval($i / 3) - 1;
      $ry = $i % 3 - 1 + $rx;

      if ($rx != 0 || $ry != 0) {
        $tmp[ $i ] = array( $rx, $ry );
      }
    }

    $t = 0;
    while (++$t < 9) {
      foreach ($tmp as $k => $d) {
        $rx = $x + $t * $d[0];
        $ry = $y + $t * $d[1];

        if ($rx < 1 || $rx > 8 || $ry < 1 || $ry > 8) {
          $tmp[ $k ] = null;
          continue;
        }

        if (!$board->isCell( $rx, $ry )) {
          $result[] = array( 'x' => $rx, 'y' => $ry, 'type' => 'move' );
        } else {
          if ($board->getCell( $rx, $ry )->getOwner() != $this->owner) {
            $result[] = array( 'x' => $rx, 'y' => $ry, 'type' => 'kill' );
          }
          $tmp[ $k ] = null;
        }
      }
    }

    return $result;
  }
}