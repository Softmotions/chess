<?php

/**
 * Обёртка для запроса.
 */
final class Request {
  private $data;

  /**
   * Создание обёртки. Post-аттрибуты заменяют get-аттрибуты.
   * Если передан дополнительный массив с атрибутами - они заменяют остальные.
   * @param array $additional дополнительные аттрибуты
   */
  public function __construct ($additional = null) {
    $this->data = array();
    $this->data = array_merge($_GET, $_POST);
    if (null != $additional && is_array($additional)) {
      $this->data = array_merge($this->data, $additional);
    }
  }

  /**
   * Проверка наличия аттрибута.
   * @param string $name имя аттрибута
   * @return bool наличие аттрибута
   */
  public function hasAttribute ($name) {
    return isset($this->data[$name]);
  }

  /**
   * Получение аттрибута.
   * @param string $name имя аттрибута
   * @return mixed значение аттрибута
   */
  public function getAttribute ($name) {
    if (!isset($this->data[$name])) {
      return null;
    }

    return $this->data[$name];
  }

  /**
   * Получение специализированного аттрибута - текущее инициализирующее действии.
   * @return string текущее инициализирующее действие
   */
  public function getIAction () {
    $action = $this->getAttribute('_action');
    if (!isset($action) || null == $action) {
      $action = '';
    }

    return strval($action);
  }

  /**
   * Получение специализированного аттрибута - текущего действия.
   * @return string текущее действие
   */
  public function getAction () {
    $action = $this->getAttribute('action');
    if (!isset($action) || null == $action) {
      $action = '';
    }

    return strval($action);
  }
}

?>