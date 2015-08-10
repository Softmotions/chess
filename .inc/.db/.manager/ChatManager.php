<?php

require_once('./.inc/.db/DBConnection.php');

require_once('./.inc/.general/Message.php');

class ChatManager {

  /**
   * @param int $key
   *
   * @return int
   */
  public static function getLastMessageId ($key = 0) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT MAX(`id`) AS \'id\' FROM `chat` WHERE `key` = ?');

    $statement->setInteger(0, $key);

    $rset = $statement->execute();

    $rset->next();
    $id = $rset->getInteger('id');

    $rset->close();
    $statement->close();

    return $id;
  }

  /**
   * @param string $message
   * @param int $time
   * @param int $uid
   * @param int $key
   */
  public static function postMessage ($message, $time, $uid, $key = 0) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
      'INSERT INTO `chat`' .
      ' ( `key`, `time`, `user`, `message` ) ' .
      'VALUES ' .
      ' ( ?, ?, ?, ? ) '
    );

    $statement->setInteger(0, $key);
    $statement->setInteger(1, $time);
    $statement->setInteger(2, $uid);
    $statement->setString(3, $message);

    $statement->executeUpdate();
    $statement->close();
  }

  /**
   * @param int $lid
   * @param int $key
   * 
   * @return array(Message)
   */
  public static function listMessages ($lid, $key = 0) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
      'SELECT * FROM `chat` WHERE `id` >= ? AND `key` = ? ORDER BY `id`'
    );

    $statement->setInteger(0, $lid);
    $statement->setInteger(1, $key);

    $result = array();

    $rset = $statement->execute();
    while ($rset->next()) {
      $result[] = new Message($rset->dataRow());
    }

    $rset->close();
    $statement->close();

    return $result;
  }

}
