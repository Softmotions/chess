<?php
require_once('./.system/config.php');
?>
<html>
<head>
  <script type="text/javascript" src="<?=CFG_JS_ROOT?>/general/prototype.js"></script>
  <script type="text/javascript" src="<?=CFG_JS_ROOT?>/general/sarissa.js"></script>
  <script type="text/javascript" src="<?=CFG_JS_ROOT?>/general/sarissa_ieemu_xpath.js"></script>
  <script type="text/javascript" src="<?=CFG_JS_ROOT?>/general/scriptutils.js"></script>
  <script type="text/javascript" src="<?=CFG_JS_ROOT?>/RequestDispatcher.js"></script>
  <link type="text/css" rel="stylesheet" href="<?=CFG_CSS_ROOT?>/chess.css">
  <title>TVEGames - Шахматы</title>
</head>
<?php

$onload = 'rqd.doGateWayRequest( \'' . CFG_WEB_ROOT . '.init\' );';

if (isset($_POST['redirect'])) {
  $redirect = $_POST['redirect'];
  unset($_POST['redirect']);

  $rparam = '{ ';
  foreach ($_POST as $key => $value) {
    $rparam .= $key . ': \'' . $value . '\', ';
  }
  $rparam .= 'container: \'main\' ';
  $rparam .= '}';

  $onload = 'rqd.doGateWayRequest( \'' . $redirect . '\', undefined, ' . $rparam . ' );';
}

?>
<body onload="RequestDispatcher.main(); <?=$onload?>">

<div id="header">
  <div class="logo">
    <a class="logoimg" href="<?=CFG_WEB_ROOT?>"><strong></strong></a>
  </div>
  <div id="chess-login">
  </div>
</div>
<div style="width: 100%; height: 90%;" id="main"></div>
</body>
</html>
