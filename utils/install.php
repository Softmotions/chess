<pre>
<?php

chdir('..');

require_once('./.system/config.php');
require_once('./.inc/.db/DBConnection.php');

$connection = DBConnection::getInstance();

echo 'Create tables: '."\n";

$connection->executeUpdate(file_get_contents('./.sql/user.sql'));
echo "\t".'`user`'."\n";
$connection->executeUpdate(file_get_contents('./.sql/fight.sql'));
echo "\t".'`fight`'."\n";
$connection->executeUpdate(file_get_contents('./.sql/fight_log.sql'));
echo "\t".'`fight_log`'."\n";
$connection->executeUpdate(file_get_contents('./.sql/fight_request.sql'));
echo "\t".'`fight_request`'."\n";
$connection->executeUpdate(file_get_contents('./.sql/chat.sql'));
echo "\t".'`chat`'."\n";

$connection->commit();

echo 'Done!'."\n";
?>