<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE); // Good to use for simple running errors

set_time_limit(60*5);



echo 'Tvheadend...<br />';



include('_config_.php');
include('func.php');



$temp['tvheadend_dvbtransports_dir'] = $config['path_tvheadend_config_dir'] . $config['path_tvheadend_dvbtransports'];
$dir_list_array = list_files_into_dir($temp['tvheadend_dvbtransports_dir']);



list($channels_sort_list,$channel_name_alias) = extract_channels_txt();
        if($config['debug'] >= 3):
echo '<pre>';
echo '<b>$channels_sort_list</b>' . "\n";
print_r($channels_sort_list);
echo '<b>$channel_name_alias</b>' . "\n";
print_r($channel_name_alias);
echo '</pre>';
        endif;



$tvheadend_dvbtransports_count = 0;

    $line_count = count($dir_list_array);
    for($i=0;$i<$line_count;$i++)
    {

// ^_dev_dvb_ ???

if($config['debug'] >= 1) echo 'dvbtransports_dir: ' . $dir_list_array[$i] . '<br/>';

unset($subdir_list_array);
$subdir_list_array = list_files_into_dir($temp['tvheadend_dvbtransports_dir'] . $dir_list_array[$i]);

            $subdir_line_count = count($subdir_list_array);
            for($ii=0;$ii<$subdir_line_count;$ii++)
            {
            
            if($config['debug'] >= 1) echo '&nbsp;&nbsp;&nbsp;' . $subdir_list_array[$ii] . '<br/>';
            
            $text = robin_file_read($temp['tvheadend_dvbtransports_dir'] . $dir_list_array[$i] . '/' . $subdir_list_array[$ii]);
            $temp_tvheadend_dvbtransports = extract_dvb_data_from_config($text,$dir_list_array[$i],$subdir_list_array[$ii]);

              // check if exist service name...            
                if(strlen($temp_tvheadend_dvbtransports['servicename']) > 0):
              $tvheadend_dvbtransports[$tvheadend_dvbtransports_count] = $temp_tvheadend_dvbtransports;
              $tvheadend_dvbtransports_count++;
                else:
              if($config['debug'] >= 2) echo 'short servicename /provider/: ' . $temp_tvheadend_dvbtransports['provider'] . '<br/>';
                endif;
                       
            }

    }





    // alias -> name
    $line_count = count($tvheadend_dvbtransports);
    for($i=0;$i<$line_count;$i++)
    {

      if(isset($channel_name_alias[$tvheadend_dvbtransports[$i]['servicename']])):
$tvheadend_dvbtransports[$i]['channelname'] = $channel_name_alias[$tvheadend_dvbtransports[$i]['servicename']];
      else:
$tvheadend_dvbtransports[$i]['channelname'] = $tvheadend_dvbtransports[$i]['servicename'];
      endif;

                  if($i == 0 && $config['debug'] >= 2):
              echo '<pre>';
              print_r($tvheadend_dvbtransports[$i]);
              echo  '</pre>';
                  endif;

    }





// Save...

$use_scrambled = 0;

    $line_count = count($tvheadend_dvbtransports);
    for($i=0;$i<$line_count;$i++)
    {

        // Only TV
        if ($tvheadend_dvbtransports[$i]['stype'] == 1 OR $tvheadend_dvbtransports[$i]['stype'] == 25):

        // $stats['all_tv_channel']++;

          if ($tvheadend_dvbtransports[$i]['scrambled'] == 1):
          $use_scrambled = scrambled_yes_or_no($tvheadend_dvbtransports[$i],$channels_sort_list);
          // $stats['scrambled_tv_channel']++;
          // if($use_scrambled === 1) $stats['scrambled_tv_channel_-_to_descramble']++;
          endif;

                if (  ($tvheadend_dvbtransports[$i]['scrambled'] == 1 && $use_scrambled === 1)  OR $tvheadend_dvbtransports[$i]['scrambled'] == 0):
                $channel_enable = 1;
                create_dvb_data($tvheadend_dvbtransports[$i],$channel_enable);
                // all enabled channel...
                $list_all_enabled_channel[$tvheadend_dvbtransports[$i]['channelname']] = 1;
                else:
                // DISABLE...
                $channel_enable = 0;
                create_dvb_data($tvheadend_dvbtransports[$i],$channel_enable);
                endif;

        // block Radio
        else:

        $channel_enable = 0;
        create_dvb_data($tvheadend_dvbtransports[$i],$channel_enable);

        endif;
        // END Only TV

    }





if($config['analog_tv'] === 1) $list_all_enabled_channel = analog_tv($list_all_enabled_channel);


        if($config['debug'] >= 3): 
echo '<pre>';
echo '<b>$list_all_enabled_channel</b>' . "\n"; 
print_r($list_all_enabled_channel);
echo '</pre>';
        endif;


$final_channels_sorted_list = sort_channels($list_all_enabled_channel,$channels_sort_list);

xbmc_sqlite($final_channels_sorted_list);



/*
echo '<b>Stats:</b><br>';
echo ' all: ' . $stats['all_tv_channel'] . '<br>';
echo ' free (FTA): ' . ($stats['all_tv_channel'] - $stats['scrambled_tv_channel']) . '<br>';
echo ' scrambled: ' . $stats['scrambled_tv_channel'] . '<br>';
echo ' to descramble: ' . $stats['scrambled_tv_channel_-_to_descramble'] . '<br>';
echo ' total to "watch": ' . ( ($stats['all_tv_channel'] - $stats['scrambled_tv_channel']) + $stats['scrambled_tv_channel_-_to_descramble']  ) . '<br>';
*/

?>