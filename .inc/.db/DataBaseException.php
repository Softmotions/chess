<?php

/**
 * Для индикации ошибок работы с БД
 */
class DataBaseException extends Exception {
  protected $source;
  protected $reason;

  /**
   * Инициализация эксепшенв.
   * @param string $source источник
   * @param string $message сообщение ошибки работы с БД
   * @param string $code код ошибки
   */
  public function __construct ($source, $message, $code) {
    parent::__construct("[DataBase Error in '" . $source . "'] " . $message, $code);

    $this->reason = $message;
    $this->source = $source;
  }

  /**
   * Возвращает источник ошибки
   * @return string источник ошибки
   */
  public function getSource () {
    return $this->source;
  }

  /**
   * Возвращает описание ошибки
   * @return string описание ошибки
   */
  public function getReason () {
    return $this->reason;
  }
}

?>