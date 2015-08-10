<?php

define('SMARTY_DIR', '/usr/lib/php5/Smarty/');

define('CFG_WEB_DIR', '/usr/local/tveprojects/chess/src/www/');
define('CFG_TEMP_DIR', '/usr/local/tveprojects/chess/tmp/');

define('CFG_TEMPLATES_DIR', CFG_WEB_DIR . 'data/templates/');
define('CFG_CONFIGS_DIR', CFG_WEB_DIR . 'data/configs/');
define('CFG_COMPILE_DIR', CFG_TEMP_DIR . 'templates_compiled/');
define('CFG_CACHE_DIR', CFG_TEMP_DIR . 'cache/');

define('CFG_WEB_ROOT', '/');
define('CFG_JS_ROOT', CFG_WEB_ROOT . 'js');
define('CFG_CSS_ROOT', CFG_WEB_ROOT . 'css');

define('CFG_DB_SERVER', 'localhost');
define('CFG_DB_LOGIN', 'chess');
define('CFG_DB_PASSWORD', 'aChe5Sho');
define('CFG_DB_DATABASE', 'th_chess');

// todo:
define('CFG_SEND_MAIL_FROM', 'chess@nsu.ru');

define('CFG_DEFAULT_EXECUTOR', 'main');

$CFG_EXECUTORS = array(
  'main' => 'MainExecutor',
  'frequest' => 'FightRequestExecutor',
  'chat' => 'ChatExecutor',
  'fight' => 'FightExecutor'
);


?>