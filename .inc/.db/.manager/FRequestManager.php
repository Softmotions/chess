<?php

require_once('./.inc/.general/FRequest.php');

require_once('./.inc/.db/DBConnection.php');

class FRequestManager {

  /**
   * @param int $id
   * @param int $userFilter
   *
   * @return array(FRequest)
   */
  public static function getAllRequest ($id, $userFilter) {
    $connection = DBConnection::getInstance();

    $statement;

    switch (intval($userFilter)) {
      case 1:
        $statement = $connection->prepare('SELECT * FROM `fight_request` WHERE `fpl` = ? OR `spl` = ?');

        $statement->setInteger(0, $id);
        $statement->setInteger(1, $id);
        break;

      case 2:
        $statement =
            $connection->prepare('SELECT * FROM `fight_request` WHERE `fpl` != ? AND ( `spl` IS NULL OR `spl` != ? )');

        $statement->setInteger(0, $id);
        $statement->setInteger(1, $id);
        break;

      case 0:
      default:
        $statement = $connection->prepare('SELECT * FROM `fight_request`');
        break;

    }

    $requests =
        array(
        );
    $resultSet = $statement->execute();

    while ($resultSet->next()) {
      $requests[] = new FRequest($resultSet->dataRow());
    }

    $resultSet->close();
    $statement->close();

    return $requests;
  }

  /**
   * @param int $uid
   *
   * @return bool
   */
  public function createRequest ($uid) {
    $connection = DBConnection::getInstance();

    //    $statement = $connection->prepare("SELECT * FROM `fight_request` WHERE `fpl` = ? OR `spl` = ?");
    //    $statement->setString( 0, $id );
    //    $statement->setString( 1, $id );
    //
    //    $resultSet = $statement->execute();
    //
    //    $exists = $resultSet->next();
    //
    //    $resultSet->close();
    //    $statement->close();
    //
    //    if ($exists) {
    //      $template->assign( 'error_report', 'А не много ли заявок, а?');
    //    } else {

    $statement = $connection->prepare('INSERT INTO `fight_request` ( `fpl`, `spl` ) VALUES ( ?, null )');
    $statement->setString(0, $uid);

    $statement->executeUpdate();
    $statement->close();

    return true;
    //    }
  }

  /**
   * @param int $uid
   * @param int $rid
   *
   * @return FRequest
   */
  public function acceptRequest ($uid, $rid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT * FROM `fight_request` WHERE `fpl` = ? AND `id` = ?');
    $statement->setInteger(0, $uid);
    $statement->setInteger(1, $rid);

    $resultSet = $statement->execute();
    $frequest = null;
    if ($resultSet->next()) {
      $frequest = new FRequest($resultSet->dataRow());
    }

    $resultSet->close();
    $statement->close();

    if (!isset($frequest) || $frequest->getSplId() == 0) {
      return null;
    }

    $statement = $connection->prepare('DELETE FROM `fight_request` WHERE `id` = ?');
    $statement->setString(0, $rid);

    $statement->executeUpdate();
    $statement->close();

    return $frequest;
  }

  /**
   * @param int $uid
   * @param int $rid
   *
   * @return bool
   */
  public function cancelRequest ($uid, $rid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('DELETE FROM `fight_request` WHERE `fpl` = ? AND `id` = ?');
    $statement->setInteger(0, $uid);
    $statement->setInteger(1, $rid);

    $statement->executeUpdate();
    $statement->close();

    return true;
  }

  /**
   * @param int $uid
   * @param int $rid
   *
   * @return bool
   */
  public function rejectRequest ($uid, $rid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('UPDATE `fight_request` SET `spl` = null WHERE `fpl` = ? AND `id` = ?');
    $statement->setInteger(0, $uid);
    $statement->setInteger(1, $rid);

    $statement->executeUpdate();
    $statement->close();

    return true;
  }

  /**
   * @param int $uid
   * @param int $rid
   *
   * @return bool
   */
  public function attachRequest ($uid, $rid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT * FROM `fight_request` WHERE `id` = ?');
    $statement->setInteger(0, $rid);

    $resultSet = $statement->execute();

    $frequest = null;
    if ($resultSet->next()) {
      $frequest = new FRequest($resultSet->dataRow());
    }

    $resultSet->close();
    $statement->close();

    if (!isset($frequest) || $frequest->getSplId() != 0) {
      return false;
    }

    $statement = $connection->prepare('UPDATE `fight_request` SET `spl` = ? WHERE `id` = ?');
    $statement->setInteger(0, $uid);
    $statement->setInteger(1, $rid);

    $statement->executeUpdate();
    $statement->close();

    return true;
  }

  /**
   * @param int $uid
   * @param int $rid
   *
   * @return bool
   */
  public function unattachRequest ($uid, $rid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('UPDATE `fight_request` SET `spl` = null WHERE `spl` = ? AND `id` = ?');
    $statement->setInteger(0, $uid);
    $statement->setInteger(1, $rid);

    $statement->executeUpdate();
    $statement->close();

    return true;
  }
}

?>