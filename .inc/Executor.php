<?php

require_once('./.inc/.general/Request.php');
require_once('./.inc/.general/User.php');

require_once('./.inc/.template/STemplate.php');

abstract class Executor {

  /**
   * @var User
   */
  protected $user;

  /**
   * @var string
   */
  protected $name;

  /**
   * @param string $name
   * @return void
   */
  public function __construct ($name) {
    $this->name = $name;
  }

  /**
   * @param User $user
   */
  public function init (User $user) {
    $this->user = $user;
  }

  /**
   * @param STemplate $template
   * @param Request $request
   */
  public abstract function doRequest (STemplate $template, Request $request);
}