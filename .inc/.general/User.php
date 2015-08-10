<?php

class User {

  private $id;

  private $login;

  private $email;

  private $active;

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
   * @return string
   */
  public function getLogin () {
    return $this->login;
  }

  /**
   * @return string
   */
  public function getEMail () {
    return $this->email;
  }

  /**
   * @return bool
   */
  public function isActive () {
    return $this->active;
  }

  /**
   * @param array $data
   */
  public function fromArray ($data) {
    if (!is_array($data)) {
      return;
    }

    $this->id = intval($data['id']);
    $this->login = strval($data['login']);
    $this->email = strval($data['email']);

    $this->active = !isset($data['activation']) || empty($data['activation']);
  }

}

?>