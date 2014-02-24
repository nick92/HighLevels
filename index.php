<?php
	
	$mysql = mysql_connect('localhost', 'root', 'hydref');
	if (!$mysql) 
	{
		die('Could not connect: ' . mysql_error());
	}
	
	$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
	get_user_info();
	
	function get_user_info()
	{
		$userInfo = mysql_query("select * from users where name = 'nick'");	
		
		$row = mysql_fetch_array($userInfo);
		
		$stat_array = explode(",",$row[2]);		
		
		foreach($stat_array as $sID)
		{	
			$site = "http://www.environment-agency.gov.uk/homeandleisure/floods/riverlevels/120766.aspx?stationId=$sID";
			$scraped_website = curl($site);
			$scraped_data = scrape_between($scraped_website, '<div class="chart-top"><h3>Current level: ', "</h3>");
			echo $scraped_data;
			unset($sID);
		}
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
	mysql_close($mysql);
?>
