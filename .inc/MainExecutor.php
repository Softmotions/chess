<?php

require_once('./.inc/Executor.php');

require_once('./.inc/.db/.manager/UserManager.php');

class MainExecutor extends Executor {

  public function doRequest(STemplate $template, Request $request) {
    if (!isset($this->user) || $this->user == null) {
      $this->doAnonymousRequest($template, $request);
    } else {
      $this->doUserRequest($template, $request);
    }
  }

  private function doAnonymousRequest(STemplate $template, Request $request) {
    $template->setOutput('login.xtpl');

    if (strlen($request->getIAction()) > 0) {
      switch ($request->getIAction()) {
        case 'index':
          $template->setOutput('info.xtpl');
          break;

        case 'register':
          $template->setOutput('register.xtpl');
          break;

        case 'reactivate':
          $template->setOutput('reactivate.xtpl');
          break;

        case 'activate':
          $login = trim($request->getAttribute('login'));
          $activation = trim($request->getAttribute('activation'));

          $status = UserManager::activate($login, $activation);

          $template->assign('status', $status);

          $template->setOutput('activate.tpl');
          break;

        default:
          $template->assign('container', 'chess-login');

          $template->setOutput('login.xtpl');
          break;
      }

      return;
    }

    switch ($request->getAction()) {
      case 'register':
        $template->setOutput('register.xtpl');

        $error = array();

        $login = trim($request->getAttribute('login'));
        $email = trim($request->getAttribute('email'));

        $password = trim($request->getAttribute('password'));
        $cpassword = trim($request->getAttribute('cpassword'));

        $template->assign('login', $login);
        $template->assign('email', $email);

        $lcheck = UserManager::checkLogin($login);

        switch ($lcheck) {
          case 1:
            $error[] = 'Невеный формат поля \'login\'.';
            break;

          case 2:
            $error[] = 'Этот \'login\' уже занят.';
            break;

          default:
            break;
        }

        $echeck = UserManager::checkEmail($email);

        switch ($echeck) {
          case 1:
            $error[] = 'Невеный формат поля \'e-mail\'.';
            break;

          case 2:
            $error[] = 'Этот \'e-mail\' уже зарегистрирован в системе.';
            break;

          default:
            break;
        }

        $pcheck = UserManager::checkPasword($password, $cpassword);

        switch ($pcheck) {
          case 1:
            $error[] = 'Не верное подтверждение пароля.';
            break;

          case 2:
            $error[] = 'Пароль слишком короткий.';
            break;

          case 3:
            $error[] = 'Пароль слишком длинный.';
            break;

          default:
            break;
        }

        if (count($error) == 0) {
          if (!UserManager::register($login, $email, $password)) {
            $error[] = 'Ошибка отправки письма с активацией';
            $template->assign('error', $error);
          }

          $template->setOutput('login.xtpl');
        }

        $template->assign('error', $error);

        break;

      case 'reactivate':
        $template->setOutput('reactivate.xtpl');

        $error = array();

        $email = trim($request->getAttribute('email'));
        $template->assign('email', $email);

        $racheck = UserManager::reactivate($email);

        switch ($racheck) {
          case 1:
            $error[] = 'Пользователь с указанным e-mail-ом не зарегистрирован.';
            break;

          case 2:
            $error[] = 'Этот аккаун уже активирован.';
            break;

          case 3:
            $error[] = 'Ошибка отправки письма с активацией';
            break;

          default:
            break;
        }

        if (count($error) == 0) {
          $template->setOutput('login.xtpl');
          break;
        }
        $template->assign('error', $error);

        break;

      case 'login':
        $template->setOutput('login.xtpl');

        $error = array();

        $login = trim($request->getAttribute('login'));
        $password = trim($request->getAttribute('password'));

        $template->assign('login', $login);

        $user = UserManager::login($login, $password);
        if (!$user) {
          $error[] = 'Ошибка аторизации.';
        } elseif (!$user->isActive()) {
          $error[] = 'Аккаунт не активирован.';
          $template->assign('activateneed', true);
        } else {
          $_SESSION['user'] = $user;
          $template->assign('user', $user);

          $template->assign('container', 'chess-login');

          $template->setOutput('init.xtpl');
          break;
        }

        $template->assign('error', $error);

        break;

      default:
        break;
    }
  }

  private function doUserRequest(STemplate $template, Request $request) {
    $template->setOutput('index.xtpl');

    if (strlen($request->getIAction()) > 0) {
      switch ($request->getIAction()) {
        case 'init':
          $template->assign('container', 'chess-login');

          $template->setOutput('init.xtpl');
          break;

        case 'index':
          break;

        case 'fight':
          $template->setOutput('fight.xtpl');

          $template->assign('fightId', $request->getAttribute('fightId'));

          break;

        default:
          $template->swapDirs('root');
          $template->setOutput('redirect.xtpl');
          break;
      }

      return;
    }

    switch ($request->getAction()) {
      case 'logout':
        session_unregister('user');
        $template->setOutput('login.xtpl');
        break;

      default:
        break;
    }
  }
}

?>