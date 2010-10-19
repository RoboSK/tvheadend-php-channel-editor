<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE); // Good to use for simple running errors

set_time_limit(60*5);



echo 'Tvheadend - muxes status -> OK...<br />';



include('_config_.php');
include('func.php');

$config['path_tvheadend_dvbmuxes'] = 'dvbmuxes/';
// $config['debug'] = 3;





// func



                      function extract_data_from_dvbmuxes($data)
                      {

global $config;

  $line = explode ("\n", $data);
  for($i=0;$i<(count($line));$i++)
  {

      if (preg_match('/"status": "([a-z0-9@#!\-\. ]+)"/i',$line[$i],$matches)):
        $output['status'] = strtolower($matches[1]);
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;status: ' . $output['status'] . '<br/>';
        $output['all'] .= '	"status": "OK",' . "\n";
        continue;
      endif;

$output['all'] .= $line[$i] . "\n";

  }

return $output;

                      }



// END func





$temp['tvheadend_dvbmuxes_dir'] = $config['path_tvheadend_config_dir'] . $config['path_tvheadend_dvbmuxes'];
$dir_list_array = list_files_into_dir($temp['tvheadend_dvbmuxes_dir']);



    $line_count = count($dir_list_array);
    for($i=0;$i<$line_count;$i++)
    {

// ^_dev_dvb_ ???

unset($subdir_list_array);
$subdir_list_array = list_files_into_dir($temp['tvheadend_dvbmuxes_dir'] . $dir_list_array[$i]);

            $subdir_line_count = count($subdir_list_array);
            for($ii=0;$ii<$subdir_line_count;$ii++)
            {
            
            $text = robin_file_read($temp['tvheadend_dvbmuxes_dir'] . $dir_list_array[$i] . '/' . $subdir_list_array[$ii]);
            $temp_tvheadend_dvbmuxes = extract_data_from_dvbmuxes($text);

            if ($temp_tvheadend_dvbmuxes['status'] != 'ok') robin_file_write($temp['tvheadend_dvbmuxes_dir'] . $dir_list_array[$i] . '/' . $subdir_list_array[$ii],$temp_tvheadend_dvbmuxes['all']);
                       
            }

    }

?> 