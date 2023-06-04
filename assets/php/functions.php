<?php

	$config_path = "/var/www/html/config.ini"; //path to config file, recommend you place it outside of web root
	Ini_Set( 'display_errors',false);
	include('Net/SSH2.php');
	$config = parse_ini_file($config_path, true);
	
	$local_pfsense_ip = $config['network']['local_pfsense_ip'];
	$local_server_ip = $config['network']['local_server_ip'];
	$pfsense_if_name = $config['network']['pfsense_if_name'];
	$ssh_username = $config['credentials']['ssh_username'];
	$ssh_password = $config['credentials']['ssh_password'];
	$forecast_api = $config['api_keys']['forecast_api'];
	$sabnzbd_api = $config['api_keys']['sabnzbd_api'];
	$weather_lat = $config['misc']['weather_lat'];
	$weather_long = $config['misc']['weather_long'];
	$filesystems = $config['filesystems'];
	$filesystemsDarpa = $config['filesystemsDarpa'];
        $filesystemsLambda = $config['filesystemsLambda'];
	#$wan1_ip = $config['wan1_ip'];
	#$ping_ip = $config['ping_ip'];
	global $sabnzbd_api;
	$sabnzbdXML = simplexml_load_file('http://darpa:8080/api?mode=queue&output=xml&apikey='.$sabnzbd_api);
	$sabnzbdXMLhistory = simplexml_load_file('http://darpa:8080/api?mode=history&limit=5&output=xml&apikey='.$sabnzbd_api);
        //$currentForecast0 = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/43.125511%2C-88.440258?unitGroup=us&include=events%2Cdays%2Chours%2Ccurrent%2Calerts&key=YF6B49NUJV9XRXQXZJCELD8K2&contentType=json'); 


function getCpuUsage()
{
        global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('lambda.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
        }
        $stream =  $ssh->exec(' sar | tail -2');
        $array = preg_split('/\s+/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
        $output = array_values($array);
	#$output1 = array_values($array1);
	#$cpuLoad1 = $output1[3];
        #$cpuLoad5 = $output1[4];
        #$cpuLoad15 = $output1[5];
	$cpuIdleAvg = $output[16];
	$cpuIdle = $output[8];
	$cpuSysAvg = $output[13];
	$cpuSys = $output[5];
	$cpuUserAvg = $output[11];
	$cpuUser = $output[3];
	global $cpuIOwait;
	$cpuIOwaitAvg = [14];
	$cpuIOwait =  [6];
	$cpuUsageAvg = 100 - $cpuIdleAvg;
	$cpuUsage = 100 - $cpuIdle;
        return array (sprintf('%.0f',($cpuUsageAvg)), $cpuUserAvg, $cpuSysAvg, $cpuIdleAvg, $cpuIOwaitAvg, sprintf('%.0f',($cpuUsage)), $cpuUser, $cpuSys, $cpuIdle, $cpuIOwait, 'CPU Usage' );
}

function getCpuIO()
{
        $cpuIOwaitAvg = getCpuUsage()[4];
        return array (sprintf('%.0f',($cpuIOwaitAvg)), 'IO Wait');
	
}
	
function makeQueue()
{
        global $sabnzbdXML;
        $tmp = $sabnzbdXML;
        $numJobs = ((string)$tmp->noofslots) - 1;
	#echo '<div class="exolight">';
        echo '<h4 class="exoregular">--- Queue ---</h4>';

#        echo "--------------------Queue--------------------";

	if ($numJobs == -1)
	{
		printQueue(-1);
	}
	else
	{
	$x = 0;
	#for ($x = 0; $x <= $numJobs; $x++){
	while ($x <= $numJobs) {
	printQueue($x);
	$x++;
	}
	}
        echo '<br/>';
        echo '<h4 class="exoregular">--- Recent ---</h4>';

        #echo "--------------------Recent--------------------";

	$y = 0;
	while ($y <= 3) {
	printQueueHistory($y);
	$y++;
	}
}


function makeCpuBars()
{
	printCpuBar(getCpuUsage()[0],getCpuUsage()[1],getCpuUsage()[2],getCpuUsage()[3],getCpuUsage()[5],getCpuUsage()[6],getCpuUsage()[7],getCpuUsage()[8],getCpuUsage()[10],getCpuUsage()[11]);
}

function makeIOwaitBars()
{
	printBar(getCpuIO()[0],getCpuIO()[1]);
}


function byteFormat($bytes, $unit = "", $decimals = 2) {
	$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 
			'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
 
	$value = 0;
	if ($bytes > 0) {
		// Generate automatic prefix by bytes 
		// If wrong prefix given
		if (!array_key_exists($unit, $units)) {
			$pow = floor(log($bytes)/log(1024));
			$unit = array_search($pow, $units);
		}
 
		// Calculate byte value by prefix
		$value = ($bytes/pow(1024,floor($units[$unit])));
	}
 
	// If decimals is not numeric or decimals is less than 0 
	// then set default value
	if (!is_numeric($decimals) || $decimals < 0) {
		$decimals = 2;
	}
 
	// Format output
	return sprintf('%.' . $decimals . 'f '.$unit, $value);
  }

function makeDiskBars()
{
	global $filesystems;
	foreach ($filesystems as $fs_index => $fs_info){
		$fs = explode(",",$fs_info);
	}
	$x = 0;
	$y = 1;
	for ($y = 1, $x = 0; $y < 7;  $y += 2, $x += 2){
        printDiskBarGB(getDiskspace($fs[$x]), getDiskspaceUsed($fs[$x]), $fs[$y]);
	}
}

function makeDiskBarsDarpa()
{
        global $filesystemsDarpa;
        foreach ($filesystemsDarpa as $fs_index => $fs_info){
                $fs = explode(",",$fs_info);
        }
        $x = 0;
        $y = 1;
        printDiskBarGB(getDiskspaceDarpa($fs[$x]), getDiskspaceUsedDarpa($fs[$x]), $fs[$y]);
}

function makeDiskBarsLambda()
{
        global $filesystemsLambda;
        foreach ($filesystemsLambda as $fs_index => $fs_info){
                $fs = explode(",",$fs_info);
        }
        $x = 0;
        $y = 1;
	for ($y = 1, $x = 0; $y < 5;  $y += 2, $x += 2){
        printDiskBarGB(getDiskspaceLambda($fs[$x]), getDiskspaceUsedLambda($fs[$x]), $fs[$y]);
	}
}

function makeRamBars()
{
	printRamBar(getFreeRam()[0],getFreeRam()[1],getFreeRam()[2],getFreeRam()[3]);
}

function getFreeRam()
{

        global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('lambda.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
        }
        $stream =  $ssh->exec('free -m ');
        $array = preg_split('/[\s]/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
	$output = array_values($array);
	$totalRam = $output[7]/1000; // GB
	$freeRam = $output[12]/1000; // GB
	$usedRam = $totalRam - $freeRam;
	return array (sprintf('%.0f',($usedRam / $totalRam) * 100), $usedRam, $totalRam, 'Used Ram');
}

function getDiskspace($dir)
{
        global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('epiphany.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
	}
	# Get pertinent mounts from epiphany server
	$stream =  $ssh->exec('df -h | grep '. $dir );
	$array = preg_split('/\s+/', $stream);
   	for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
        	}
	$number = str_replace("%", "", $array[4]);
	return $number;
}

function getDiskspaceDarpa($dir)
{
	global $ssh_username;
        global $ssh_password;

        $ssh = new Net_SSH2('darpa.local');
	if (!$ssh->login($ssh_username,$ssh_password)) {
        exit('Login Failed');
        }
         //Get pertinent mounts from darpa server
        $stream =  $ssh->exec('df -h | grep '. $dir );
        $array = preg_split('/\s+/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
        $number = str_replace("%", "", $array[4]);
        return $number;
}

function getDiskspaceLambda($dir)
{
        global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('lambda.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
        }
        $stream =  $ssh->exec('df -h | grep '. $dir );
        $array = preg_split('/\s+/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
        $number = str_replace("%", "", $array[4]);
        return $number;
}

function getDiskspaceUsed($dir)
{

        global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('epiphany.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
	}


	$stream =  $ssh->exec('df -h | grep '. $dir );
	$array = preg_split('/\s+/', $stream);
   	for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
        	}
	$du = $array[2];
	$dt = $array[1];
	$compactarray = array($du, $dt); 
	return $compactarray;

}

function getDiskspaceUsedDarpa($dir)
{
	global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('darpa.local');
	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
        }


        $stream =  $ssh->exec('df -h | grep '. $dir );
        $array = preg_split('/\s+/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
        $du = $array[2];
        $dt = $array[1];
        $compactarray = array($du, $dt);
        return $compactarray;
}

function getDiskspaceUsedLambda($dir)
{
	global $ssh_username;
	global $ssh_password;
        $ssh = new Net_SSH2('lambda.local');

	if (!$ssh->login($ssh_username,$ssh_password)){
        exit('Login Failed');
        }
        $stream =  $ssh->exec('df -h | grep '. $dir );
        $array = preg_split('/\s+/', $stream);
        for ($i=count($array)-1; $i>=0; $i--) {
                if ($array[$i] == '') unset ($array[$i]);
                }
        $du = $array[2];
        $dt = $array[1];
        $compactarray = array($du, $dt);
        return $compactarray;
}

// Not used
//function getLoad($id)
//{
//	return 100 * ($GLOBALS['loads'][$id] / 8);
//}

function printBar($value, $name = "")
{
	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($value, 0) . "%";
		echo '<div class="progress">';
			echo '<div class="progress-bar" style="width: ' . $value . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function printCpuBar($percentavg, $cpuUserAvg, $cpuSysAvg, $cpuIdleAvg, $percent, $cpuUser, $cpuSys, $cpuIdle, $name = "")
{
        if ($percentavg < 80)
        {
                $progress = "progress-bar";
        }
        else if (($percentavg >= 80) && ($percentavg < 95))
        {
                $progress = "progress-bar progress-bar-warning";
        }
        else
        {
                $progress = "progress-bar progress-bar-danger";
        }
        // 4/6/2023 I took this out as I didnt get much value out of avg cpu usage. If you want to add it back in you will need to add $nameavg back into the function printCpuBar variable pass
        //if ($nameavg != "") echo '<!-- ' . $nameavg . ' -->';
        //echo '<div class="exolight">';
        //       if ($nameavg != "")
        //                echo $nameavg . ": ";
        //                echo number_format($percentavg, 0) . "%";
        //        echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . number_format($cpuUserAvg, 2) . '% (User)   |   ' . number_format($cpuSysAvg, 2) . '% (Sys)   |   ' . number_format($cpuIdleAvg, 2) . '% (Idle)" class="progress">';
        //                echo '<div class="'. $progress .'" style="width: ' . $percentavg . '%"></div>';
        //        echo '</div>';
        //echo '</div>';

	 if ($percent < 80)
        {
                $progress = "progress-bar";
        }
        else if (($percent >= 80) && ($percent < 95))
        {
                $progress = "progress-bar progress-bar-warning";
        }
        else
        {
                $progress = "progress-bar progress-bar-danger";
        }
        if ($name != "") echo '<!-- ' . $name . ' -->';
        echo '<div class="exolight">';
                if ($name != "")
                        echo $name . ": ";
                        echo number_format($percent, 0) . "%";
                echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . number_format($cpuUser, 2) . '% (User)   |   ' . number_format($cpuSys, 2) . '% (Sys)   |   ' . number_format($cpuIdle, 2) . '% (Idle)" class="progress">';
                        echo '<div class="'. $progress .'" style="width: ' . $percent . '%"></div>';
                echo '</div>';
        echo '</div>';


}

function printRamBar($percent, $used, $total, $name = "")
{
	if ($percent < 90)
	{
		$progress = "progress-bar";
	}
	else if (($percent >= 90) && ($percent < 95))
	{
		$progress = "progress-bar progress-bar-warning";
	}
	else
	{
		$progress = "progress-bar progress-bar-danger";
	}

	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($percent, 0) . "%";
		echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . number_format($used, 2) . ' GB / ' . number_format($total, 0) . ' GB" class="progress">';
			echo '<div class="'. $progress .'" style="width: ' . $percent . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function printDiskBarGB($dup, $dsu, $name = "")
{
	if ($dup < 85)
	{
		$progress = "progress-bar";
	}
	else if (($dup >= 85) && ($dup < 95))
	{
		$progress = "progress-bar progress-bar-warning";
	}
	else
	{
		$progress = "progress-bar progress-bar-danger";
	}
	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($dup, 0) . "%";
		echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . $dsu[0] . ' / ' . $dsu[1] . '" class="progress">';
			echo '<div class="'. $progress .'" style="width: ' . $dup . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function printQueue($num)
{

	global $sabnzbdXML;

	if ($num < 0)
	{
		echo '<div class="exolight">';
        	echo "Clear";
	        echo '</div>';
	}
	else
	{
	$tmp = $sabnzbdXML;
        $name = (string)$tmp->slots->slot[$num]->filename;
	$order = ($num + 1);
        echo '<div class="exolight"; max-height:100;>';
	echo $order . "." . $name . "\n" ;
	echo '</div>';
	}
}

function printQueueHistory($numHistory)
{

        global $sabnzbdXMLhistory;

        if ($numHistory < 0)
        {

                echo "Clear";
        }
        else
        {
        $tmpHistory = $sabnzbdXMLhistory;
        $nameHistory = (string)$tmpHistory->slots->slot[$numHistory]->name;
        #var_dump($nameHistory);
        $order = ($numHistory + 1);
        echo '<div class="exolight";>';
        echo $order . "." . $nameHistory . "\n" ;
        echo '</div>';
        }
}

function ping()
{
	global $local_server_ip;
        $pingIP = '8.8.8.8';
        $avgPing = round(shell_exec("ping -c 5 " . $pingIP . " | grep dev | awk -F '/' '{print $5}'" ));
        return $avgPing;
}

function comfort()
{
        $results = array();
        $curl = curl_init();
        // Temp and Humidity queries
        $q0 =  curl_escape($curl , 'SELECT "value" FROM "Humidity" WHERE "location"=\'HumidorTop\' ORDER BY desc LIMIT 1');
        $q1 =  curl_escape($curl , 'SELECT "value" FROM "Humidity" WHERE "location"=\'Main\' ORDER BY desc LIMIT 1');
        $q2 =  curl_escape($curl , 'SELECT "value" FROM "Humidity" WHERE "location"=\'Basement\' ORDER BY desc LIMIT 1');
        $q3 =  curl_escape($curl , 'SELECT "value" FROM "Temperature" WHERE "location"=\'Basement\' ORDER BY desc LIMIT 1');
        $q4 =  curl_escape($curl , 'SELECT "value" FROM "Temperature" WHERE "location"=\'Main\' ORDER BY desc LIMIT 1');
        $q5 =  curl_escape($curl , 'SELECT "value" FROM "Temperature" WHERE "location"=\'Nursery\' ORDER BY desc LIMIT 1');
        $q6 =  curl_escape($curl , 'SELECT "value" FROM "Temperature" WHERE "location"=\'JacobsRM\' ORDER BY desc LIMIT 1');
        $q7 =  curl_escape($curl , 'SELECT "value" FROM "Temperature" WHERE "location"=\'NataliesRM\' ORDER BY desc LIMIT 1');

        for ($i = 0; $i < 8; $i++) {
                $url = "http://grafana.local:8086/query?db=Custom&q=${"q".$i}";
                curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => $url,
                        CURLOPT_SSL_VERIFYPEER => false, // If You have https://
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_CUSTOMREQUEST => "GET",
                     ));
                     // Send the request & save response to $resp
                     $resp = curl_exec($curl);
                     if( !$resp ){
                        // log this Curl ERROR: 
                        echo curl_error($curl) ;
                     }
                     curl_close($curl);
                     $output = explode("," , $resp);
                     $value = preg_replace('/\}|\]/', '', $output[4]);
                     $results[] = $value;
                     $humidor = sprintf('%.0f',($results[0]));
                     $tempBasement = sprintf('%.0f',($results[3] * .10));
                     $tempMain =  sprintf('%.0f',($results[4] * .10));
                     $tempNursery = sprintf('%.0f',($results[5] * .10));
                     $tempJacob = sprintf('%.0f',($results[6] * .10));
                     $tempNatalie = sprintf('%.0f',($results[7] * .10));
                }

                echo '<h4 class="exoregular">Humidity</h4>';
                echo '<h5 class="exoregular" style="white-space: nowrap; padding-left: 1em;">Main: '.$results[1].'% | Basement: '.$results[2].'%</h5>';
                echo '<h5 class="exoregular" style="white-space: nowrap;">Humidor: '.$humidor.'% </h5>';
                echo '<h4 class="exoregular">Temperature</h4>';
                echo '<h5 class="exoregular" style="white-space: nowrap;">Main: '.$tempMain.'F | Nursery: '.$tempNursery.'F</h5>';
                echo '<h5 class="exoregular" style="white-space: nowrap;">Jacob\'s Rm: '.$tempJacob.'F | Natalie\'s Rm: '.$tempNatalie.'F</h5>';

}

function makeBandwidthBars()
{
	$array = getBandwidth();
	$dPercent = sprintf('%.0f',($array[0] / 450) * 100);
	$uPercent = sprintf('%.0f',($array[1] / 24) * 100);
	$external_ip =  `curl -s ipinfo.io/ip`;
	echo '<div class="exolight">';
	echo "External IP: $external_ip";
	echo '<br/>';
	echo '<br/>';
	printBandwidthBar($dPercent, $array[0], 'Download');
	printBandwidthBar($uPercent, $array[1], 'Upload' );
}

function getBandwidth()
{
        global $local_pfsense_ip;
	global $ssh_username;
	global $ssh_password;
	global $pfsense_if_name;
	$ssh = new Net_SSH2($local_pfsense_ip);
	if (!$ssh->login($ssh_username,$ssh_password)) { // replace password and username with pfSense ssh username and password if you want to use this
		exit('Login Failed');
	}

	$dump = $ssh->exec('vnstat -i '.$pfsense_if_name.' -tr');
	$output = preg_split('/[\.|\s]/', $dump);

	for ($i=count($output)-1; $i>=0; $i--) {
		if ($output[$i] == '') unset ($output[$i]);
	}
	$output = array_values($output);
	$rxRate = $output[16] . '.' . $output[17];
	$rxFormat = $output[18];
	$txRate = $output[22] . '.' . $output[23];
	$txFormat = $output[24];
	if ($rxFormat == 'kbit/s') {
		$rxRateMB = $rxRate / 1024;
	} else {
		$rxRateMB = $rxRate;
	}
	if ($txFormat == 'kbit/s') {
		$txRateMB = $txRate / 1024;
	} else {
		$txRateMB = $txRate;
	}
	$rxRateMB = floatval($rxRateMB);
	$txRateMB = floatval($txRateMB);
 	// Gests highest bandwidth offender
	return  array($rxRateMB, $txRateMB);
}

function printBandwidthBar($percent, $Mbps, $name = "")
{
	if ($name != "") echo '<!-- ' . $name . ' -->';
	echo '<div class="exolight">';
		if ($name != "")
			echo $name . ": ";
			echo number_format($Mbps,2) . " Mbps";
		echo '<div class="progress">';
			echo '<div class="progress-bar" style="width: ' . $percent . '%"></div>';
		echo '</div>';
	echo '</div>';
}

function getDir($b)
{
   $dirs = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
   return $dirs[round($b/45)];
}

function uvindex($weatherdata_json)
{
        #$currentForecast0 = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/43.125511%2C-88.440258?unitGroup=us&include=events%2Cdays%2Chours%2Ccurrent%2Calerts&key=YF6B49NUJV9XRXQXZJCELD8K2&contentType=json');
        $currentForecast = json_decode($weatherdata_json);
        $uvindex = $currentForecast->currentConditions->uvindex;
        return $uvindex;
}

function getWeatherData()
{
        $weatherdata_json = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/43.125511%2C-88.440258?unitGroup=us&include=events%2Cdays%2Chours%2Ccurrent%2Calerts&key=YF6B49NUJV9XRXQXZJCELD8K2&contentType=json');
        return $weatherdata_json;
}

function makeNewWeatherSidebar($weatherdata_json)
{
        global $weather_lat;
        global $weather_long;
        global $currentForecast0;
	$forecastLat = $weather_lat;
	$forecastLong = $weather_long;
        $currentHour = date('H') + 1;
        //$currentForecast0 = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/43.125511%2C-88.440258?unitGroup=us&include=events%2Cdays%2Chours%2Ccurrent%2Calerts&key=YF6B49NUJV9XRXQXZJCELD8K2&contentType=json'); 
	//$currentForecast0 = file_get_contents('https://api.openweathermap.org/data/3.0/onecall?lat=43.125511&lon=-88.440258&units=Imperial&appid=06891a1a22ef724a6ca0504ec55e4642');
        $currentForecast = json_decode($weatherdata_json);
        $currentSummary = $currentForecast->currentConditions->conditions;
	$dailySummary = $currentForecast->daily->summary;
        $currentSummaryIcon = $currentForecast->currentConditions->icon;
        $currentTemp = round($currentForecast->currentConditions->temp);
        $currentWindSpeed = round($currentForecast->currentConditions->windspeed);
        if ($currentWindSpeed > 0) {
                $currentWindBearing = $currentForecast->currentConditions->winddir;
        }
        $hourlySummary = $currentForecast->days[0]->hours[$currentHour]->conditions;
        $NextDaySummary = $currentForecast->days[1]->description;
        $sunriseTime = $currentForecast->currentConditions->sunriseEpoch;
        $sunsetTime = $currentForecast->currentConditions->sunsetEpoch;

        if ($sunriseTime > time()) {
                $rises = 'Rises';
        } else {
                $rises = 'Rose';
        }

        if ($sunsetTime > time()) {
                $sets = 'Sets';
        } else {
                $sets = 'Set';
        }

        // If there are alerts, make the alerts variables
        if (isset($currentForecast->alerts)&& !empty($currentForecast->alerts)) {
                $alertCount = count($currentForecast->alerts);
                for ($i = 0; $i < $alertCount; $i++) {
                        if (strpos($currentForecast->alerts[$i]->event, 'Air Quality') !== false) {  //I do not care about air quality alerts so skip them
                                continue; // Skip this alert and move to the next iteration
                            }
                        $alertTitle = $currentForecast->alerts[$i]->event;
                        $alertExpires = $currentForecast->alerts[$i]->headline;
                        $alertDescription = $currentForecast->alerts[$i]->description;
                        $alertUri = $currentForecast->alerts[$i]->link;
                        //If there is a severe weather warning, display it
                        echo '<div class="alert alert-danger text-center alert-dismissable">';
                        echo '  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        echo '  <strong style="font-size: 12px; display: inline-block; width: 100%; text-align: center;">';
                        echo '    <a href="'.$alertUri.'" class="alert-link">'.$alertTitle.'</a>';
                        echo '  </strong>';
                        echo '</div>';
                }
        }
        // Make the array for weather icons
        $weatherIcons = [
                'clear-day' => 'B',
                'clear-night' => 'C',
                'rain' => 'R',
                'snow' => 'W',
                'sleet' => 'X',
                'wind' => 'F',
                'fog' => 'L',
                'cloudy' => 'N',
                'partly-cloudy-day' => 'H',
                'partly-cloudy-night' => 'I',
        ];
        $weatherIcon = $weatherIcons[$currentSummaryIcon];
         //If there is a severe weather warning, display it
        echo '<ul class="list-inline" style="margin-bottom:-20px">';
        echo '<li><h1 data-icon="'.$weatherIcon.'" style="font-size:500%;margin:0px -10px 20px -5px"></h1></li>';
        echo '<li><ul class="list-unstyled">';
        echo '<li><h1 class="exoregular" style="margin:0px">'.$currentTemp.'°</h1></li>';
        echo '<li><h4 class="exoregular" style="margin:0px;padding-right:10px;width:80px">'.$currentSummary.'</h4></li>';
        echo '</ul></li>';
        echo '</ul>';
        if ($currentWindSpeed > 0) {
              $direction = getDir($currentWindBearing);
               echo '<h4 class="exoextralight" style="margin-top:0px">Wind: '.$currentWindSpeed.' mph from the '.$direction.'</h4>';
        } else {
                echo '<h4 class="exoextralight" style="margin-top:0px">Wind: Calm</h4>';
        }
        echo '<h4 class="exoregular">Next Hour</h4>';
        echo '<h5 class="exoextralight" style="margin-top:10px">'.$hourlySummary.'</h5>';
        echo '<h4 class="exoregular">The Sun</h4>';
       //echo '<h5 class="exoextralight" style="margin-top:10px">'.$rises.' at '.date('g:i A', $sunriseTime).'</h5>';
        echo '<h5 class="exoextralight" style="margin-top:10px">'.$sets.' at '.date('g:i A', $sunsetTime).'</h5>';
        echo '<h4 class="exoregular">Next 24 Hours</h4>';
        echo '<h5 class="exoextralight" style="margin-top:10px">'.$NextDaySummary.'</h5>';
        echo '<p class="text-right no-link-color" style="margin-bottom:-10px"><small><a href="https://www.windy.com/-Weather-radar-radar?radar,42.910,-88.746,10">Windy.com</a></small></p> ';    
        //echo '<p class="text-right no-link-color" style="margin-bottom:-10px"><small><a href="index2.php">test.io</a></small></p> ';
}

function makeWeatherForecast($weatherdata_json)
{
        global $weather_lat;
        global $weather_long;
	$forecastLat = $weather_lat;
	$forecastLong = $weather_long;
        $currentHour = date('H') + 1;
        //$currentForecast0 = file_get_contents('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/43.125511%2C-88.440258?unitGroup=us&include=events%2Cdays%2Chours%2Ccurrent%2Calerts&key=YF6B49NUJV9XRXQXZJCELD8K2&contentType=json'); 
        $currentForecast = json_decode($weatherdata_json);
        $currentSummary = $currentForecast->currentConditions->conditions;
	$dailySummary = $currentForecast->daily->summary;
        $currentSummaryIcon = $currentForecast->currentConditions->icon;
        $currentTemp = round($currentForecast->currentConditions->temp);
        $currentWindSpeed = round($currentForecast->currentConditions->windspeed);
        if ($currentWindSpeed > 0) {
                $currentWindBearing = $currentForecast->currentConditions->winddir;
        }
        $hourlySummary = $currentForecast->days[0]->hours[$currentHour]->conditions;
        $NextDaySummary = $currentForecast->days[1]->description;

        // Make the array for weather icons
        $weatherIcons = [
                'clear-day' => 'B',
                'clear-night' => 'C',
                'rain' => 'R',
                'snow' => 'W',
                'sleet' => 'X',
                'wind' => 'F',
                'fog' => 'L',
                'cloudy' => 'N',
                'partly-cloudy-day' => 'H',
                'partly-cloudy-night' => 'I',
        ];
        $weatherIcon = $weatherIcons[$icon];
        echo '<ul class="list-inline" style="margin-bottom:-20px">';
        for ($i = 0; $i <= 6; $i++) {
        $date = $currentForecast->days[$i]->datetime ; // the date you want to check
        $dayOfWeek = substr(date('l', strtotime($date)), 0, 3); // get the day of the week as a string
        $maxTemp = round($currentForecast->days[$i]->tempmax);
        $minTemp = round($currentForecast->days[$i]->tempmin);
        $icon = $currentForecast->days[$i]->icon;
        $weatherIcon = $weatherIcons[$icon];

            echo '<li>';
            echo '<h4 class="exoregular" style="margin:10px">'.$dayOfWeek.'</h4>';
            echo '<h1 data-icon="'.$weatherIcon.'" style="font-size:250%;margin:0px -10px 20px -10px"></h1>';
            echo '<h4 class="exoregular" style="margin:0px">'.$maxTemp.'°</h4>';
            echo '<h4 class="exoregular" style="margin:0px 0px 20px 0px">'.$minTemp.'°</h4>';
            echo '</li>';
        }
        echo '</ul>';

}
?>
