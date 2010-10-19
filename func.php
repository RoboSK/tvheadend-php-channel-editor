<?php


                      function list_files_into_dir($dir)
                      {

// 19.07.10

//define the path as relative
$path = $dir;

//using the opendir function
$dir_handle = @opendir($path) or die("Unable to open $path");

// echo "Directory Listing of $path<br/>";

//running the while loop
while ($file = readdir($dir_handle)) 
{

if($file == '.' OR $file == '..') continue;

//   echo "<a href='$file'>$file</a><br/>";
   $file_list_array[] = $file;
}

//closing the directory
closedir($dir_handle);

return $file_list_array;

                      }











		if (!function_exists(robin_file_read)):

function robin_file_read($filename_and_path)
{

$file=@FOpen($filename_and_path, 'rb');
@flock($file,1);
$data = @fread($file, filesize($filename_and_path));
@flock($file,3);
@fclose($file);

return $data;

}

		endif;






                      function extract_dvb_data_from_config($data,$dir,$filename)
                      {

// 20.07.10

global $config;


  $line = explode ("\n", $data);
  for($i=0;$i<(count($line));$i++)
  {

/*
...tv...
"stype": 1,  
...radio...
"stype": 2,
...HD ???...
"stype": 25,

...YES...
"scrambled": 1,
...NO...
"scrambled": 0,

...if "channelname" then 1, if not then empty...
"mapped": 1,

"provider": "Skylink",
"servicename": "TV JOJ",
"channelname": "TV JOJ",
*/

      if (preg_match('/"provider": "([a-z0-9@#!\-\. ]+)"/i',$line[$i],$matches)):
        $output['provider'] = strtolower($matches[1]);
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;provider: ' . $output['provider'] . '<br/>';
        continue;
      endif;

      if (preg_match('/"servicename": "(.+)"/i',$line[$i],$matches)):
        $output['servicename'] = $matches[1];
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;servicename: ' . $output['servicename'] . '<br/>';
        $output['all'] .= '---INSERT---' . "\n";
        continue;
      endif;

      if (preg_match('/"scrambled": ([0-9]),/i',$line[$i],$matches)):
        $output['scrambled'] = $matches[1];
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;scrambled: ' . $output['scrambled'] . '<br/>';
        continue;
      endif;

      if (preg_match('/"stype": ([0-9]+),/i',$line[$i],$matches)):
        $output['stype'] = $matches[1];
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;stype: ' . $output['stype'] . '<br/>';
        continue;
      endif;

      if (preg_match('/"mapped": ([0-9]),/i',$line[$i],$matches)):
        $output['mapped'] = $matches[1];
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;mapped: ' . $output['mapped'] . '<br/>';
        continue;
      endif;

      if (preg_match('/"disabled": ([0-9]),/i',$line[$i],$matches)):
        $output['disabled'] = $matches[1];
        if($config['debug'] >= 2) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;disabled: ' . $output['disabled'] . '<br/>';
        continue;
      endif;

      // del old channelname...
      if (preg_match('/"channelname": "(.+)"/i',$line[$i],$matches)):
        continue;
      endif;

$output['all'] .= $line[$i] . "\n";

  }

$output['dir'] = $dir;
$output['filename'] = $filename;

return $output;

                      }
















                      function extract_channels_txt()
                      {

// 19.07.10

global $config;

$counter = 0;

$data = robin_file_read($config['file_channels_list']);

  $line = explode ("\n", $data);
  for($i=0;$i<(count($line));$i++)
  {

// skipp comments...
if(preg_match('#^//#i',$line[$i])) continue;

$tmp_ex = explode('#',$line[$i]);

// check if exist channel name...
// if(!strlen($tmp_ex[1]) > 0) continue;
if(!isset($tmp_ex[1])) continue;


// ignore comment
if (preg_match('#^//#i',$line[$i])) continue;


// ID...
    if($tmp_ex[0] > 0):
$channel_id = $tmp_ex[0];
    elseif($tmp_ex[0] == '*'):
$channel_id = 0;
    else:
$channel_id = NULL;
    endif;


// Name...
$tmp_ex_name = explode('|',$tmp_ex[1]);
$channel_name = $tmp_ex_name[0];

    // if exist alias...make alias list...
    if(count($tmp_ex_name) > 1):
      for($ii=0;$ii<(count($tmp_ex_name));$ii++)
      {
$channel_name_alias[$tmp_ex_name[$ii]] = $tmp_ex_name[0];
      }
    else:
$channel_name_alias[$tmp_ex_name[0]] = $tmp_ex_name[0];
    endif;


    // scrambled...
    if(isset($tmp_ex[2])):

    $channel_scrambled = 1;

      if(preg_match("#(([0-9]{5})_([A-Z]{1}))#i",$tmp_ex[2],$matches)):
      // freq.
      // 11797_H -> TS202011797000_H
      $channel_scrambled_type = 1;
      $channel_scrambled_string = 'TS2020' . $matches[2] . '000' . '_' . strtoupper($matches[3]);
      else:
      // provider
      $channel_scrambled_type = 2;
      $channel_scrambled_string = strtolower($tmp_ex[2]);
      endif;

    else:
    $channel_scrambled = 0;
    $channel_scrambled_type = NULL;
    $channel_scrambled_string = NULL;
    endif;


        // generate list
        if(isset($channel_name)):
$channels_sort_list[$counter]['id'] = $channel_id;
$channels_sort_list[$counter]['name'] = $channel_name;

$channels_sort_list[$counter]['scrambled'] = $channel_scrambled;
$channels_sort_list[$counter]['scrambled_type'] = $channel_scrambled_type;
$channels_sort_list[$counter]['scrambled_string'] = $channel_scrambled_string;

$counter++;
        endif;

  }

return array($channels_sort_list,$channel_name_alias);

                      }




















                      function scrambled_yes_or_no($dvbtransports,$channels_sort_list)
                      {

// 20.07.10

    $line_count = count($channels_sort_list);
    for($i=0;$i<$line_count;$i++)
    {

        if($channels_sort_list[$i]['name'] == $dvbtransports['channelname']):

              // freq.
              if($channels_sort_list[$i]['scrambled'] === 1 AND $channels_sort_list[$i]['scrambled_type'] === 1):
  if (preg_match('/' . $channels_sort_list[$i]['scrambled_string'] . '/i',$dvbtransports['filename'],$matches)):
return 1;
  endif;
              endif;

              // provider
              if($channels_sort_list[$i]['scrambled'] === 1 AND $channels_sort_list[$i]['scrambled_type'] === 2):
  if ($channels_sort_list[$i]['scrambled_string'] == $dvbtransports['provider']):
return 1;
  endif;

              endif;

        endif;

    }

return 0;

                      }


















                      function create_dvb_data($dvbtransports,$channel_enable)
                      {

// 20.07.10

global $config;

$add_text = NULL;

$add_text = '	"provider": "' . $dvbtransports['provider'] . '",
	"servicename": "' . $dvbtransports['servicename'] . '",
	"scrambled": ' . $dvbtransports['scrambled'] . ',
	"stype": ' . $dvbtransports['stype'] . ',
';

                if($channel_enable === 1):

$add_text .= '	"channelname": "' . $dvbtransports['channelname'] . '",
	"mapped": 1,
	"disabled": 0,';

                else:

$add_text .= '	"disabled": 1,';

                endif;



$output = preg_replace('#---INSERT---#i',$add_text, $dvbtransports['all']);

robin_file_write($config['path_tvheadend_config_dir_output'] . $config['path_tvheadend_dvbtransports'] . $dvbtransports['dir'] . '/' . $dvbtransports['filename'],$output);

                      }

















                      function sort_channels($list_all_enabled_channel,$channels_sort_list)
                      {

// 20.07.10

global $config;
global $config_tv_logo;

/*
>0   = ID
0    = next empty
null = next from xyz
*/

$counter = 0;

    $line_count = count($channels_sort_list);
    for($i=0;$i<$line_count;$i++)
    {

        // if is defined channel number...
        if($channels_sort_list[$i]['id'] > 0 && $list_all_enabled_channel[$channels_sort_list[$i]['name']] === 1):

$final_channels_sorted_list[$counter]['channel_number'] = $channels_sort_list[$i]['id'];
$final_channels_sorted_list[$counter]['name'] = $channels_sort_list[$i]['name'];

$counter++;

unset($list_all_enabled_channel[$channels_sort_list[$i]['name']]);

        endif;

    }



    $line_count = count($channels_sort_list);
    for($i=0;$i<$line_count;$i++)
    {

        // if is channel number defines as 0...use next empty...
        if($channels_sort_list[$i]['id'] === 0 && $list_all_enabled_channel[$channels_sort_list[$i]['name']] === 1):

$next_empty_id = return_next_empty_id($final_channels_sorted_list);

$final_channels_sorted_list[$counter]['channel_number'] = $next_empty_id;
$final_channels_sorted_list[$counter]['name'] = $channels_sort_list[$i]['name'];

$counter++;

unset($list_all_enabled_channel[$channels_sort_list[$i]['name']]);

        endif;


    }



$actual_max_channel_number = 0;
    // Find Max Channel Number...
    $line_count = count($final_channels_sorted_list);
    for($i=0;$i<$line_count;$i++)
    {
        if(isset($final_channels_sorted_list[$i]['channel_number'])):
      if($final_channels_sorted_list[$i]['channel_number'] > $actual_max_channel_number) $actual_max_channel_number = $final_channels_sorted_list[$i]['channel_number'];
        endif;
    }



$other_channel_number_begin_from = ceiling(($actual_max_channel_number+$config['add_to_rounding']), 100);



    $line_count = count($channels_sort_list);
    for($i=0;$i<$line_count;$i++)
    {

        // if is channel number defines as NULL...use next empty bigger then...
        if(!isset($channels_sort_list[$i]['id']) && $list_all_enabled_channel[$channels_sort_list[$i]['name']] === 1):

$next_empty_id = return_next_empty_id($final_channels_sorted_list,$other_channel_number_begin_from);

$final_channels_sorted_list[$counter]['channel_number'] = $next_empty_id;
$final_channels_sorted_list[$counter]['name'] = $channels_sort_list[$i]['name'];

$counter++;

unset($list_all_enabled_channel[$channels_sort_list[$i]['name']]);

        endif;


    }



        if($config['debug'] >= 3):
echo '<pre>';
echo '<b>$list_all_enabled_channel (' . count($list_all_enabled_channel) . ')</b>' . "\n";
print_r($list_all_enabled_channel);
echo '</pre>';
        endif;



ksort($list_all_enabled_channel,SORT_STRING);



        if($config['debug'] >= 3):
echo '<pre>';
echo '<b>SORT -> $list_all_enabled_channel (' . count($list_all_enabled_channel) . ')</b>' . "\n";
print_r($list_all_enabled_channel);
echo '</pre>';
        endif;



    // all other channel...
    foreach ($list_all_enabled_channel as $k => $v)
    {

$next_empty_id = return_next_empty_id($final_channels_sorted_list,$other_channel_number_begin_from);

$final_channels_sorted_list[$counter]['channel_number'] = $next_empty_id;
$final_channels_sorted_list[$counter]['name'] = $k;

$counter++;

unset($list_all_enabled_channel[$k]);

    }



        if($config['debug'] >= 3):
echo '<pre>';
echo '<b>$final_channels_sorted_list (' . count($final_channels_sorted_list) . ')</b>' . "\n";
print_r($final_channels_sorted_list);
echo '</pre>';
        endif;



// remove old ch. list
$temp['tvheadend_channels_dir'] = $config['path_tvheadend_config_dir'] . $config['path_tvheadend_channels']; 
$dir_list_array = list_files_into_dir($temp['tvheadend_channels_dir']);

    $line_count = count($dir_list_array);
    for($i=0;$i<$line_count;$i++)
    {
@unlink($config['path_tvheadend_config_dir_output'] . $config['path_tvheadend_channels'] . $dir_list_array[$i]);
    }



    $line_count = count($final_channels_sorted_list);
    for($i=0;$i<$line_count;$i++)
    {

        // TV Logo
        if($config['tv_logo'] === 1):
  if(isset($config_tv_logo[$final_channels_sorted_list[$i]['name']])):
$temp_tv_logo = "\n" . '	"icon": "' . $config['path_tv_logo_-_tvheadend'] . $config_tv_logo[$final_channels_sorted_list[$i]['name']] . '",';
  else:
$temp_tv_logo = NULL;
  endif;
        endif;

$output = '{
	"name": "' . $final_channels_sorted_list[$i]['name'] . '",' . $temp_tv_logo . '
	"tags": [
	],
	"dvr_extra_time_pre": 0,
	"dvr_extra_time_post": 0,
	"channel_number": ' . (int) $final_channels_sorted_list[$i]['channel_number'] . '
}
';

// write new channels...
robin_file_write($config['path_tvheadend_config_dir_output'] . $config['path_tvheadend_channels'] . ($i+1),$output);

    }

return $final_channels_sorted_list;

                      }


















                      function xbmc_sqlite($final_channels_sorted_list)
                      {

// 20.07.10

global $config;
global $config_tv_logo;

$file = $config['path_xbmc_sqlite'];

@unlink($file);


$db = new SQLite3($file);

$db->exec('CREATE TABLE ChannelGroup (idGroup integer primary key, groupName text, sortOrder integer)');
$db->exec('CREATE TABLE ChannelLinkageMap (idMapping integer primary key, idPortalChannel integer, idLinkedChannel integer)');
$db->exec('CREATE TABLE ChannelSettings ( idChannel integer primary key, Deinterlace integer,ViewMode integer,ZoomAmount float, PixelRatio float, AudioStream integer, SubtitleStream integer,SubtitleDelay float, SubtitlesOn bool, Brightness float, Contrast float, Gamma float,VolumeAmplification float, AudioDelay float, OutputToAllSpeakers bool, Crop bool, CropLeft integer,CropRight integer, CropTop integer, CropBottom integer, Sharpness float, NoiseReduction float)');
$db->exec('CREATE TABLE Channels (idChannel integer primary key, Name text, Number integer, ClientName text, ClientNumber integer, idClient integer, UniqueId integer, IconPath text, GroupID integer, countWatched integer, timeWatched integer, lastTimeWatched datetime, encryption integer, radio bool, hide bool, grabEpg bool, EpgGrabber text, lastGrabTime datetime, Virtual bool, strInputFormat text, strStreamURL text)');
$db->exec('CREATE TABLE Clients (idClient integer primary key, Name text, GUID text)');
$db->exec('CREATE TABLE GuideData (idDatabaseBroadcast integer primary key, idUniqueBroadcast integer, idChannel integer, StartTime datetime, EndTime datetime, strTitle text, strPlotOutline text, strPlot text, GenreType integer, GenreSubType integer, firstAired datetime, parentalRating integer, starRating integer, notify integer, seriesNum text, episodeNum text, episodePart text, episodeName text)');
$db->exec('CREATE TABLE LastChannel (idChannel integer primary key, Number integer, Name text)');
$db->exec('CREATE TABLE LastEPGScan (idScan integer primary key, ScanTime datetime)');
$db->exec('CREATE TABLE RadioChannelGroup (idGroup integer primary key, groupName text, sortOrder integer)');
$db->exec('CREATE TABLE version (idVersion integer, iCompressCount integer)');
$db->exec('CREATE UNIQUE INDEX ix_ChannelSettings ON ChannelSettings (idChannel)');
$db->exec('CREATE UNIQUE INDEX ix_GuideData on GuideData(idChannel, StartTime desc)');

$db->exec('INSERT INTO version VALUES(4,0)');
$db->exec('INSERT INTO Clients VALUES(1,\'Tvheadend HTSP Client\',\'pvr.hts\')');



    $line_count = count($final_channels_sorted_list);
    for($i=0;$i<$line_count;$i++)
    {

        // TV Logo
        if($config['tv_logo'] === 1):
  if(isset($config_tv_logo[$final_channels_sorted_list[$i]['name']])):
$temp_tv_logo = '\'' . $config['path_tv_logo_-_xbmc'] . $config_tv_logo[$final_channels_sorted_list[$i]['name']] . '\'';
  else:
$temp_tv_logo = '\'\'';
  endif;
        else:
$temp_tv_logo = '\'\'';
        endif;

$temp_channel_number = ($config['xbmc_channel_id_one_by_one'] === 1) ? ($i+1) : $final_channels_sorted_list[$i]['channel_number'];
$channel_name_for_sql = preg_replace('/\'/','\'\'',$final_channels_sorted_list[$i]['name']);

$db->exec('INSERT INTO Channels VALUES(' . ($i+1) . ',\'' . $channel_name_for_sql . '\',' . (int) $temp_channel_number . ',\'' . $channel_name_for_sql . '\',' . ($i+1) . ',1,' . ($i+1) . ',' . $temp_tv_logo . ',0,NULL,NULL,NULL,0,0,0,1,\'client\',NULL,0,\'\',\'\')');

  if(isset($config['xbmc_lastchannel']) && $final_channels_sorted_list[$i]['name'] == $config['xbmc_lastchannel']):
$db->exec('INSERT INTO LastChannel VALUES(1,' . (int) $temp_channel_number . ',\'' . $channel_name_for_sql . '\')');
  endif;
    }

                      }

















                      function analog_tv($list_all_enabled_channel)
                      {

// 20.07.10

global $config;

list($analog_tv_services,$list_all_enabled_channel) = analog_tv_create_services($list_all_enabled_channel);

@mkdir($config['path_tvheadend_config_dir_output'] . $config['path_analog_tv_services']);

$dir_list_array = list_files_into_dir($config['path_tvheadend_config_dir'] . $config['path_analog_tv_adapters']);

    $line_count = count($dir_list_array);
    for($i=0;$i<$line_count;$i++)
    {

@mkdir($config['path_tvheadend_config_dir_output'] . $config['path_analog_tv_services'] . $dir_list_array[$i]);

              // delete old...
              $dir_list_array_old_services = list_files_into_dir($config['path_tvheadend_config_dir_output'] . $config['path_analog_tv_services'] . $dir_list_array[$i]);
              
              $line_count_2 = count($dir_list_array_old_services);
              for($ii=0;$ii<$line_count_2;$ii++)
              {
              @unlink($config['path_tvheadend_config_dir_output'] . $config['path_analog_tv_services'] . $dir_list_array[$i] . '/' . $dir_list_array_old_services[$ii]);
              }

                        $line_count_3 = count($analog_tv_services);
                        for($iii=0;$iii<$line_count_3;$iii++)
                        {
robin_file_write($config['path_tvheadend_config_dir_output'] . $config['path_analog_tv_services'] . $dir_list_array[$i] . '/' . $dir_list_array[$i] . '_' . ($iii+1),$analog_tv_services[$iii]);
                        }

    }

return $list_all_enabled_channel;

                      }










                      function analog_tv_create_services($list_all_enabled_channel)
                      {

// 20.07.10

global $config;
global $channel_name_alias;

$data = robin_file_read($config['file_analogtv_channels_list']);

      $line = explode ("\n", $data);
      for($i=0;$i<(count($line));$i++)
      {

$tmp_ex = explode('#',$line[$i]);

if(!isset($tmp_ex[0]) OR !isset($tmp_ex[1])) continue;

preg_match("#(([0-9]{3}),([0-9]{2}))#i",$tmp_ex[1],$matches);

$frequency = $matches[2] . $matches[3] . '0000';

  // alias...
  if(isset($channel_name_alias[$tmp_ex[0]])): 
$channelname = $channel_name_alias[$tmp_ex[0]];
  else:
$channelname = $tmp_ex[0];
  endif;

      // only unique channel
      if($config['analog_tv_only_unique_channel'] === 1):
$tmp_disabled = ($list_all_enabled_channel[$channelname] === 1) ? 1 : 0;
      else:
$tmp_disabled = 0;
      endif;


  // temp ("fix" Tvheadend bug) - disabling dont works...
  if($tmp_disabled === 0):

$output[] = '{
	"frequency": ' . $frequency . ',
	"channelname": "' . $channelname . '",
	"mapped": 1,
	"pcr": 0,
	"disabled": ' . (int) $tmp_disabled . ',
	"stream": {
		"pid": 4294967295,
		"type": "MPEG2AUDIO",
		"position": 0
	},
	"stream": {
		"pid": 4294967295,
		"type": "MPEG2VIDEO",
		"position": 0
	}
}
';

$list_all_enabled_channel[$channelname] = 1;

  endif;

      }

return array($output,$list_all_enabled_channel);

                      }















// duplicates m$ excel's ceiling function
if( !function_exists('ceiling') )
{
    function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }
}

/*
echo ceiling(0, 1000);     // 0
echo ceiling(1, 1);        // 1000
echo ceiling(1001, 1000);  // 2000
echo ceiling(1.27, 0.05);  // 1.30
*/











                      function return_next_empty_id($input_array,$min_id=NULL)
                      {

// 20.07.10

$max_id = 10000;

$min_id = ($min_id > 0) ? $min_id : 1;

    for($i=$min_id;$i<($max_id + 1);$i++)
    {

$used = 0;

        $line_count = count($input_array);
        for($ii=0;$ii<$line_count;$ii++)
        {
        if($input_array[$ii]['channel_number'] == $i) $used = 1;
        }

if($used === 0) return $i;

    }

                      }













                function return_string_x_count($string,$count)
                {

// 19.9.10

  for($i=0;$i<$count;$i++)
  {
$out .= $string;
  }

return $out;

                }





















		if (!function_exists(robin_file_write)):

function robin_file_write($filename_and_path,$data)
{

@umask(0131);
$file=@FOpen($filename_and_path, 'wb+');
@flock($file,2);
@FPutS($file,$data);
@flock($file,3);
@FClose($file);

}

		endif;











?>