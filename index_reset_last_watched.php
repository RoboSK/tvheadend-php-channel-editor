<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE); // Good to use for simple running errors

set_time_limit(10);



echo 'Tvheadend - Reset Last Watched...<br />' . "\n";



// include('_config_.php');
// include('func.php');



$config['xbmc_lastchannel'] = 'STV 1';

// $lastwatched = mktime()-(60*60*48);
$lastwatched = mktime()+(60*60*48);

$channel_name_for_sql = preg_replace('/\'/','\'\'',$config['xbmc_lastchannel']);

// $config['path_xbmc_sqlite_user'] = 'tv';
// $config['path_xbmc_sqlite'] = '/home/' . $config['path_xbmc_sqlite_user'] . '/.xbmc/userdata/Database/TV12.db';
$config['path_xbmc_sqlite'] = $_SERVER['HOME'] . '/.xbmc/userdata/Database/TV18.db';

$file = $config['path_xbmc_sqlite'];


              if (file_exists($file))
              {

echo 'Update...<br />' . "\n";

$db = new SQLite3($file);
$db->exec('UPDATE channels SET iLastWatched=\'' . (int) $lastwatched . '\' WHERE sChannelName=\'' . $channel_name_for_sql . '\'');
$db->close();

              }

?>