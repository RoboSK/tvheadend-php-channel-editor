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

$config['use_xbmc'] = 1;
$config['path_xbmc_sqlite'] = $config['path_tvheadend_config_dir_output'] . 'MyTV4.db';
$config['xbmc_lastchannel'] = 'STV 1';
$config['xbmc_channel_id_one_by_one'] = 1;


// EDIT -> 1=use analogTV, 0=NO analogTV
$config['analog_tv'] = 1;
// EDIT -> 1=only unique channel, 0=ALL
$config['analog_tv_only_unique_channel'] = 1;

$config['path_analog_tv_adapters'] = 'v4ladapters/';
$config['path_analog_tv_services'] = 'v4lservices/';

$config['file_analogtv_channels_list'] = './channels_analogtv.txt';

$config['add_to_rounding'] = 50;



// Logo
$config['tv_logo'] = 1;
if($config['tv_logo'] === 1) include('_config_tv_logo_.php');



$config['list_to_print_separator'] = ' ';
$config['list_to_print_path_filename'] = $config['path_tvheadend_config_dir_output'] . 'channel_list.txt';

$config['list_to_print_xbmc_path_filename'] = $config['path_tvheadend_config_dir_output'] . 'channel_list_xbmc.txt';

?>