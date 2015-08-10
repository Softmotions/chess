<?php

error_reporting( 0 );

require_once './.system/config.php';

require_once './.inc/.general/Request.php';
require_once './.inc/.general/User.php';
require_once './.inc/.general/DataInformer.php';

require_once './.inc/.template/STemplate.php';

require_once './.inc/.db/DBConnection.php';

session_start();

$execName = CFG_DEFAULT_EXECUTOR;
if (isset($_REQUEST['_executor'])) {
  $execName = $_REQUEST['_executor'];
}

$user;
if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof User)) {
  $execName = CFG_DEFAULT_EXECUTOR;
} else {
  $user = $_SESSION['user'];
}

if (!isset($CFG_EXECUTORS[$execName])) {
  $execName = CFG_DEFAULT_EXECUTOR;
}

require_once './.inc/' . $CFG_EXECUTORS[$execName] . '.php';

if (file_exists('./.system/config.' . $execName . '.php')) {
  require_once './.system/config.' . $execName . '.php';
}

$executor = new $CFG_EXECUTORS[$execName]($execName);

$template = new STemplate();

$template->markDirs('root');

$template->expandDirs($execName);
$template->markDirs('executor');

$template->assign('executor', $execName);
$template->assign('container', isset($_POST['container']) ? trim($_POST['container']) : $execName);
$template->assign('WEB_ROOT', CFG_WEB_ROOT);
$template->assign('WEB_LOCATION', CFG_WEB_ROOT . $execName . '/');
$template->assign('JS_ROOT', CFG_JS_ROOT);
$template->assign('JS_LOCATION', CFG_JS_ROOT . '/' . $execName);
$template->assign('CSS_ROOT', CFG_CSS_ROOT);
$template->assign('CSS_LOCATION', CFG_CSS_ROOT . '/' . $execName);

$template->register_object('dataloader', DataInformer::getInstance());

if (isset($user)) {
  $executor->init($user);
//  $template->assign('user', $user);
}

$request = new Request();

$executor->doRequest($template, $request);

$user;
if (isset($_SESSION['user']) || $_SESSION['user'] instanceof User) {
  $user = $_SESSION['user'];
  $template->assign('user', $user);
}

$template->show();

DBConnection::getInstance()->commit();
DBConnection::getInstance()->close();

?>