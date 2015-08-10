<?php

require_once( './.inc/.chess/Board.php' );

abstract class Figure {

  private $id;

  protected $owner;

  protected $type;

  private $x;

  private $y;

  public function __construct( $id, $owner ) {
    $this->id = $id;
    $this->owner = $owner;
  }

  public function getId() {
    return $this->id;
  }

  public function getOwner() {
    return $this->owner;
  }

  public function getType() {
    return $this->type;
  }

  public function getX() {
    return $this->x;
  }

  public function getY() {
    return $this->y;
  }

  public function getXY() {
    return array( 'x' => $this->x, 'y' => $this->y );
  }

  public function setXY( $x, $y ) {
    $this->x = $x;
    $this->y = $y;
  }

  public abstract function getAvailableMoves( Board $board );
}


?>