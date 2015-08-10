<?php

class Message {

  /**
   * @var int
   */
  private $id;

  /**
   * @var int
   */
  private $time;

  /**
   * @var int
   */
  private $user;

  /**
   * @var int
   */
  private $key;

  /**
   * @var string
   */
  private $message;

  /**
   * @param array $data
   */
  public function __construct($data) {
    $this->id = intval($data['id']);
    $this->time = intval($data['time']);
    $this->key = intval($data['key']);
    $this->user = intval($data['user']);
    $this->message = strval($data['message']);
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return int
   */
  public function getUserId() {
    return $this->user;
  }

  /**
   * @return int
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * @return int
   */
  public function getTime() {
    return $this->time;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }
}
