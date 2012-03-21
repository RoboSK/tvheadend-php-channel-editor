<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE); // Good to use for simple running errors

set_time_limit(10);



echo 'Tvheadend - Reset to correct settings...<br />' . "\n";



// include('_config_.php');
// include('func.php');



$config['path_xbmc_sqlite'] = $_SERVER['HOME'] . '/.xbmc/userdata/Database/TV18.db';

$file = $config['path_xbmc_sqlite'];


              if (file_exists($file))
              {

echo 'Update...<br />' . "\n";

$db = new SQLite3($file);



// Disable Crop
$db->exec('UPDATE channelsettings SET bCrop=\'0\'');

// WideZoom
$db->exec('UPDATE channelsettings SET iViewMode=\'3\'');

// Audio
$db->exec('UPDATE channelsettings SET iAudioStream=\'0\'');

// Subtitle
$db->exec('UPDATE channelsettings SET iSubtitleStream=\'0\'');



$db->close();

              }

?>