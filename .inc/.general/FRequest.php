<?php

class FRequest {

  private $id;

  private $fpl;

  private $spl;

  /**
   * @param array $data
   */
  public function __construct( $data = array() ) {
    $this->fromArray( $data );
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
   * @param array $data
   */
  public function fromArray ($data) {
    if (!is_array($data)) {
      return;
    }

    $this->id = intval($data['id']);
    $this->fpl = intval($data['fpl']);
    $this->spl = intval($data['spl']);
  }
}

?>