<?php
$location = $_POST['loc'];
$longlat = $_POST['lonlat'];

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
	
	function create_json($location, $data)
	{
		$file = "weather/" . $location . ".json";
		$put = file_put_contents($file, $data);
	}
	$data = curl("https://api.forecast.io/forecast/06446ae7099feacb17ffef78fdf89f0a/$longlat");
	create_json($location, $data);

?>
