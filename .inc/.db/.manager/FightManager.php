<?php

require_once('./.inc/.chess/Board.php');
require_once('./.inc/.general/FRequest.php');

class FightManager {

  /**
   * @param FRequest $frequest
   *
   * @return bool
   */
  public static function createFight (FRequest $frequest) {
    $connection = DBConnection::getInstance();

    $board = new Board();
    $board->initDefaultBoard();

    $statement = $connection->prepare('INSERT INTO `fight` ( `fpl`, `spl`, `dump`) VALUES ( ?, ?, ?)');
    $statement->setInteger(0, $frequest->getFplId());
    $statement->setInteger(1, $frequest->getSplId());
    $statement->setString(2, $board->shortDump());

    $statement->executeUpdate();
    $statement->close();

    return true;
  }

  /**
   * @param int $fid
   * @param int $uid
   *
   * @return Board
   */
  public static function loadFight ($fid, $uid) {
    $connection = DBConnection::getInstance();

    $board = null;

    $statement = $connection->prepare('SELECT * FROM `fight` WHERE `id` = ?');
    $statement->setInteger(0, $fid);

    $rset = $statement->execute();
    if ($rset->next()) {
      $board = new Board();
      $board->fromArray($rset->dataRow());
      $board->setRPlayer($board->getFplId() == $uid ? 0 : 1);
    }

    $rset->close();
    $statement->close();

    return $board;
  }

  /**
   * @param int $fid
   * @param int $state
   */
  public static function finishFight ($fid, $state) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('UPDATE `fight` SET `win` = ?, `active` = ? WHERE `id` = ?');
    $statement->setInteger(0, $state);
    $statement->setInteger(1, 0);
    $statement->setInteger(2, $fid);

    $statement->executeUpdate();
    $statement->close();
  }

  /**
   * @param int $fid
   * @param bool $fpl
   * @param bool $attach
   */
  public static function updateAttached ($fid, $fpl, $attach) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
      'UPDATE `fight` ' .
      'SET `' . ($fpl ? 'fatch' : 'satch') . '` = ? ' .
      'WHERE `id` = ? '
    );

    $statement->setInteger(0, $attach ? 1 : 0);
    $statement->setInteger(1, $fid);

    $statement->executeUpdate();
  }

  /**
   * @param int $uid
   *
   * @return array(int)
   */
  public static function getNonAttachedActiveFightIds ($uid) {
    $connection = DBConnection::getInstance();

    $result =
        array(
        );

    $statement = $connection->prepare(
      'SELECT `id` ' .
      'FROM `fight` ' .
      'WHERE ' .
      '   `active` = ? ' .
      '   AND ' .
      '   ( ' .
      '       ( `fpl` = ? AND `fatch` = ? ) ' .
      '       OR ' .
      '       ( `spl` = ? AND `satch` = ? ) ' .
      '   )'
    );

    $statement->setInteger(0, 1);
    $statement->setInteger(1, $uid);
    $statement->setInteger(2, 0);
    $statement->setInteger(3, $uid);
    $statement->setInteger(4, 0);

    $rset = $statement->execute();
    while ($rset->next()) {
      $result[] = $rset->getInteger('id');
    }

    $rset->close();
    $statement->close();

    return $result;
  }

  /**
   * @return array(Board)
   */
  public static function getActiveFights () {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT * FROM `fight` WHERE `active` = ?');
    $statement->setInteger(0, 1);

    $result = array();
    $rset = $statement->execute();
    while ($rset->next()) {
      $board = new Board();
      $board->fromArray($rset->dataRow());
      $result[] = $board;
    }

    $rset->close();
    $statement->close();

    return $result;
  }

  /**
   * @param Board $board
   *
   * @return void
   */
  public static function saveFight(Board $board) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
          'UPDATE `fight` '.
          'SET `player` = ?, `turn` = ?, `aturn` = ?, `lrok` = ?, `srok` = ?, `dump` = ?, `smove`= ? '.
          'WHERE `id` = ?'
    );
    $statement->setInteger(0, $board->getPlayer());
    $statement->setInteger(1, $board->getTurn());
    $statement->setInteger(2, $board->getATurn());
    $statement->setInteger(3, $board->getLRok());
    $statement->setInteger(4, $board->getSRok());
    $statement->setString(5, $board->shortDump());
    $statement->setInteger(6, $board->getSMove());
    $statement->setInteger(7, $board->getId());

    $statement->executeUpdate();
    $statement->close();
  }

  /**
   * @param Board $board
   *
   * @return void
   */
  public static function saveFightLog(Board $board) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
      'INSERT INTO `fight_log` ( `fight`, `player`, `dump`, `turn`, `adata` ) VALUES ( ?, ?, ?, ?, ? )'
    );

    $statement->setInteger(0, $board->getId());
    $statement->setInteger(1, $board->getPlayer());
    $statement->setString(2, $board->shortDump());
    $statement->setInteger(3, $board->getTurn());
    $statement->setInteger(4, $board->getSMove() + 32 * $board->getLRok() + 128 * $board->getSRok());

    $statement->executeUpdate();
    $statement->close();
  }

  /**
   * @param int $fid
   * 
   * @return bool
   */
  public static function check3StateRepeate($fid) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare(
      'SELECT COUNT(*) AS \'flcount\' '.
      'FROM `fight_log` '.
      'WHERE `fight` = ? '.
      'GROUP BY `dump`, `player`, `adata` '.
      'HAVING flcount > 2'
    );

    $statement->setInteger(0, $fid);

    $resultSet = $statement->execute();

    $result = $resultSet->next();

    $resultSet->close();
    $statement->close();

    return $result;    
  }
}

?>