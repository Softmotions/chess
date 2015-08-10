<?php

require_once('./.inc/.db/DBConnection.php');

require_once('./.inc/.general/User.php');

class UserManager {

  /**
   * @param string $login
   * @param string $password
   * @return User
   */
  public static function login ($login, $password) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT * FROM `user` WHERE `login` = ? AND `password` = ?');

    $statement->setString(0, $login);
    $statement->setString(1, md5($password));

    $rset = $statement->execute();

    $user = null;
    if ($rset->next()) {
      $user = new User($rset->dataRow());
    }

    $rset->close();
    $statement->close();

    return $user;
  }

  public static function register ($login, $email, $password) {
    $activation = md5($login . rand(0, 999999) . $email . rand(0, 999999) . $password);

    $connection = DBConnection::getInstance();

    $statement =
        $connection->prepare('INSERT INTO `user` ( `login`, `email`, `password`, `activation` ) VALUES ( ?, ?, ?, ? )');

    $statement->setString(0, $login);
    $statement->setString(1, $email);
    $statement->setString(2, md5($password));
    $statement->setString(3, $activation);

    $statement->executeUpdate();

    return self::sendRegistrationMail($login, $email, $password, $activation);
  }

  public static function reactivate ($email) {
    $connection = DBConnection::getInstance();

    $statement =
        $connection->prepare('SELECT * FROM `user` WHERE `email` = ?');

    $statement->setString(0, $email);

    $data = null;

    $rset = $statement->execute();
    if ($rset->next()) {
      $data = $rset->dataRow();
    }

    $rset->close();

    if (!$data) {
      return 1;
    }

    if (!isset($data['activation']) || empty($data['activation'])) {
      return 2;
    }

    if (!self::sendRegistrationMail($data['login'], $email, null, $data['activation'])) {
      return 3;
    }

    return 0;
  }

  public static function activate ($login, $activation) {
    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT `id` FROM `user` WHERE `login` = ? AND `activation` = ?');

    $statement->setString(0, $login);
    $statement->setString(1, $activation);

    $rset = $statement->execute();

    if ($rset->next()) {
      $userId = $rset->getInteger('id');
    }

    $rset->close();
    $statement->close();

    if ($userId) {
      $statement = $connection->prepare('UPDATE `user` SET `activation` = NULL WHERE `id` = ?');

      $statement->setInteger(0, $userId);

      $statement->executeUpdate();

      return true;
    }

    return false;
  }

  public static function checkLogin ($login) {
    if (!preg_match('/[a-zA-Zа-яА-Я0-9 ]{3,15}/', $login)) {
      return 1;
    }

    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT COUNT(*) FROM `user` WHERE `login` = ?');

    $statement->setString(0, $login);

    $rset = $statement->execute();

    $result = false;
    if ($rset->next()) {
      if ($rset->getInteger(0) == 0) {
        $result = true;
      }
    }

    $rset->close();
    $statement->close();

    return $result ? 0 : 2;
  }

  public static function checkEmail ($email) {
    if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email)) {
      return 1;
    }

    $email = strtolower($email);

    $connection = DBConnection::getInstance();

    $statement = $connection->prepare('SELECT COUNT(*) FROM `user` WHERE `email` = ?');

    $statement->setString(0, $email);

    $rset = $statement->execute();

    $result = false;
    if ($rset->next()) {
      if ($rset->getInteger(0) == 0) {
        $result = true;
      }
    }

    $rset->close();
    $statement->close();

    return $result ? 0 : 2;
  }

  public static function checkPasword ($password, $cpassword) {
    if (strcmp($password, $cpassword)) {
      return 1;
    }

    if (strlen($password) < 5) {
      return 2;
    }

    if (strlen($password) > 25) {
      return 3;
    }

    return 0;
  }

  public static function sendRegistrationMail ($login, $email, $password, $activation) {
    $aurl =
        'http://' . $_SERVER['HTTP_HOST'] . CFG_WEB_ROOT . '.activate?' .
        'login=' . urlencode($login) . '&' .
        'activation=' . urlencode($activation) . '&';

    $message =
        '
Поздравляем!
Вы зарегистрированы в браузерной online-игре "Шахматы".

Ваши учётные данные:
-------------------
Логин: ' . $login . '
' . ($password != null ? 'Пароль: ' . $password : '') . '
-------------------

Для активации учётной записи перейдите по ссылке: ' . $aurl . '

Приятной игры!

------
  С уважением,
     The Hell Corporation';

    $headers = '';
    $headers .= 'From: ' . CFG_SEND_MAIL_FROM . "\r\n";
    $headers .= 'Reply-To: ' . CFG_SEND_MAIL_FROM . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/plain; charset=UTF-8';

    return mail($email, 'Регистрация в online-шахматах', trim($message), $headers, '-f'.CFG_SEND_MAIL_FROM);
  }
}

?>