<?php

require_once('./.inc/.db/DBConnection.php');

require_once('./.inc/.general/User.php');

class DataInformer {

  private static $instance;

  private $USER_DATA;

  /**
   * @return DataInformer
   */
  public static function getInstance () {
    if (!self::$instance) {
      self::$instance = new DataInformer();
    }

    return self::$instance;
  }

  private function __construct () {
    $this->USER_DATA = array();
  }

  public function getUser ($params, &$smarty_obj) {
    return $this->_getUser($params['id']);
  }

  public function _getUser ($id) {
    if (!isset($this->USER_DATA[intval($id)])) {
      $connection = DBConnection::getInstance();

      $statement = $connection->prepare('SELECT * FROM `user` WHERE `id` = ?');

      $statement->setInteger(0, $id);

      $rset = $statement->execute();

      if ($rset->next()) {
        $user = new User($rset->dataRow());

        $this->USER_DATA[intval($id)] = $user;
      }

      $rset->close();
      $statement->close();
    }

    return $this->USER_DATA[intval($id)];
  }

  public function formatHHMMSS ($params, &$smarty_obj) {
    return $this->_formatHHMMSS($params['time']);
  }

  public function _formatHHMMSS ($time) {
    return date('H:i:s', $time);
  }

}

?>