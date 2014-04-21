<?php
	
	$data = $_GET['data'];
	$user = $_COOKIE['user'];
	
	$mysql = mysql_connect('localhost', 'root', 'hydref');
	if (!$mysql) 
	{
		die('Could not connect: ' . mysql_error());
	}
	
	$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
	$favriversquery = mysql_query("select river_id from userRivers where user_name = '$user'");
	$river = mysql_query("select stationID from rivers");
	
	while($row = mysql_fetch_array($river))
	{
		$rivers[] = $row[0];
	}	
	
	while($fav = mysql_fetch_array($favriversquery))
	{
		$favrivers[] = $fav[0];
	}	
	get_json();
	
	function get_river_level()
	{	
		$rivers = $GLOBALS['rivers']; 
		$ra = [];
		foreach($rivers as $sID)
		{	
			$site = "http://www.environment-agency.gov.uk/homeandleisure/floods/riverlevels/120766.aspx?stationId=$sID";
			$scraped_website = curl($site);
			$scraped_data = scrape_between($scraped_website, '<div class="chart-top"><h3>Current level: ', "</h3>");
			array_push($ra, $scraped_data);
			unset($sID);
		}
		return $ra;
	}
	
	function curl($url)
	{
		$options = Array(
				CURLOPT_RETURNTRANSFER => TRUE,  // Setting cURL's option to return the webpage data
				CURLOPT_FOLLOWLOCATION => TRUE,  // Setting cURL to follow 'location' HTTP headers
				CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
				CURLOPT_CONNECTTIMEOUT => 120,   // Setting the amount of time (in seconds) before the request times out
				CURLOPT_TIMEOUT => 120,  // Setting the maximum amount of time for cURL to execute queries
				CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
				CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",  
				CURLOPT_URL => $url, 
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function scrape_between($data, $start, $end)
	{
		$data = stristr($data, $start);
		$data = substr($data, strlen($start));
		$stop = stripos($data, $end);
		$data = substr($data, 0, $stop);
		return $data;
	}
	
	function get_json()
	{
		//Connect to DB to get user location
		$user = $GLOBALS['user'];
		$userQu = mysql_query("select location from users where name = '$user'");	
		$jsonArr = [];
		$myArr = [];
		$userLoc = mysql_fetch_array($userQu);
		$rivers = $GLOBALS['rivers'];
		$favrivers = $GLOBALS['favrivers'];
		$level = get_river_level();
		$addedRiv = '';
		$i = 0;
		$j = 0;
		
		foreach($rivers as $river)
		{
			//connect to DB and get river COORDS
			$riverData = mysql_fetch_array(mysql_query("select name from rivers where stationID = '".mysql_real_escape_string($river)."'"));	
			$riverQu = mysql_query("select longlat from rivers where stationID = '$river'");	
			$rivArray = mysql_fetch_array($riverQu);
			
			$addedRiv .=  $rivArray[0] . "|";
			array_push($myArr, $riverData[0]);
			array_push($myArr, $level[$i++]);
			
			//if in array add to favourties
			if(in_array($river, $favrivers))
			{
				array_push($myArr, "1");
			}
			else
			{
				array_push($myArr, "0");
			}
		}
		
		//request data from google api
		$request = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$userLoc[0]&destinations=$addedRiv&sensor=false";
		$scrapped_data = curl($request);
		$json_data = json_decode($scrapped_data);
		$disArry = [];
		$riverJson['time'] = time();
		foreach($json_data->rows as $rows)
		{
			
			foreach($rows->elements as $data)
			{
				$distance = $data->distance->text;
				$data = $_GET['type'];
				$riverJson['name'] = $myArr[$j++];
				$riverJson['level'] = $myArr[$j++];
				
				switch($data)
				{
					case '0':
						$disArry[$riverJson['name']] = intval($distance);
						break;
					case '1':
						$disArry[$riverJson['name']] = floatval($riverJson['level']);
						break;
				}
				
				$riverJson['distance'] = $distance;
				$riverJson['fav'] = $myArr[$j++];
				
				array_push($jsonArr, $riverJson);
			}
		}
		
		switch($data)
		{
			case '0':
				array_multisort($disArry, SORT_ASC, $jsonArr);
				break;
			case '1':
				array_multisort($disArry, SORT_DESC, $jsonArr);
				break;
		}
		file_put_contents("weather/rivers.json", json_encode($jsonArr));
		echo json_encode($jsonArr);
	}
	mysql_close($mysql);
?>
