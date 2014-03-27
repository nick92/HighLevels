<?php
$location = "weather";
$longlat = $_POST['lonlat'];
$check = $_POST['check'];
$update = $_POST['update'];

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
	$put = file_put_contents($file, $data) or die ("Couldn't insert text"); 
}

if($check == 'true')
{
	$timeNow = time();

	$get = file_get_contents("weather/" . $location . ".json");
	$json = json_decode($get);
	$fileTime = $json->{'currently'}->{'time'} + 7200;
	$fileLoc = $json->{'latitude'} . "," .$json->{'longitude'};
	echo $timeNow . "," . $fileTime;
	if($timeNow > $fileTime)
	{
		$data = curl("https://api.forecast.io/forecast/06446ae7099feacb17ffef78fdf89f0a/$longlat?units=si");	
		create_json($location, $data);
		echo "updated";
	}
	else
		echo "not updated";	
}
else
{
	echo 'saved';
	$data = curl("https://api.forecast.io/forecast/06446ae7099feacb17ffef78fdf89f0a/$longlat?units=si");
	create_json($location, $data);
}
?>
