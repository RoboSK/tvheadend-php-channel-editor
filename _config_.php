<?php

// $config['debug'] = 3;

// EDIT -> Path to Tvheadend configuration
$config['path_tvheadend_config_dir'] = './_data_/';
// $config['path_tvheadend_config_dir'] = 'I:/---del-iba-temp-!/tvheadend/';
// $config['path_tvheadend_config_dir'] = '/home/hts/.hts/tvheadend/';

$config['path_tvheadend_dvbtransports'] = 'dvbtransports/';

$config['file_channels_list'] = './channels.txt';

// $config['path_tvheadend_config_dir_output'] = './_data_out_/';
$config['path_tvheadend_config_dir_output'] = $config['path_tvheadend_config_dir'];

$config['path_tvheadend_channels'] = 'channels/';

$config['path_xbmc_sqlite'] = $config['path_tvheadend_config_dir_output'] . 'MyTV4.db';


// EDIT -> 1=use analogTV, 0=NO analogTV
$config['analog_tv'] = 1;

$config['path_analog_tv_adapters'] = 'v4ladapters/';
$config['path_analog_tv_services'] = 'v4lservices/';

$config['file_analogtv_channels_list'] = './channels_analogtv.txt';

$config['add_to_rounding'] = 50;

?>