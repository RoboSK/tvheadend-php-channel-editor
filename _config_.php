<?php

// $config['debug'] = 3;

$config['path_tvheadend_config_dir'] = './_data_/';
// $config['path_tvheadend_config_dir'] = 'I:/---del-iba-temp-!/tvheadend/';
// $config['path_tvheadend_config_dir'] = '/home/hts/.hts/tvheadend/';

$config['path_tvheadend_dvbtransports'] = 'dvbtransports/';

$config['file_channels_list'] = './channels.txt';

// $config['path_tvheadend_config_dir_output'] = './_data_out_/';
$config['path_tvheadend_config_dir_output'] = $config['path_tvheadend_config_dir'];

$config['path_tvheadend_channels'] = 'channels/';

$config['path_xbmc_sqlite'] = $config['path_tvheadend_config_dir_output'] . 'MyTV4.db';

$config['path_analog_tv_adapters'] = 'v4ladapters/';
$config['path_analog_tv_services'] = 'v4lservices/';

$config['file_analogtv_channels_list'] = './analogtv_channels.txt';

$config['add_to_rounding'] = 50;

?>