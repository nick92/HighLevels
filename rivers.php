<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css"> 
		<link href='http://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<title>High Levels - The home of river levels in the UK</title>
	</head>
	<body>
		<a href="index.html" id="hl">High Levels</a>
		<h4 id="title" style="margin-left: 80px;">The home of river levels in the UK</h4> 			
		<p id="user"></p>
		<div id="rImage"></div>
		<div class='rivBox' id="rb">
			
		</div>
		<div id="weather-box">
			<div id="icon"></div>
			<div id="location"></div>
			<div id="weather"></div>
			<div id="hourly"></div>
			<div id="time"></div>
			<div id="daily"></div>
		</div>
		<div id="future-weather">
			
		</div>
		<div id="search-gr">
			<form action="rivers.php" id="search-form">
				<fieldset>
					<input type="search" id="search" name="search" />
					<input type="submit" id="search-submit" value="Sub" />
				</fieldset>
			</form>
			<table style="width:300px" id="results">
				
			</table>
		</div>
		
		<script type="text/javascript"
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAo9AJzvnqD2HNX3VkM7spK8JN1F3YEs6Y&sensor=false">
		</script>
		
		<script src="riverinfo.js"></script>
			<?php 
				$river = $_GET['search'];
				echo "<p id='rTitle'>$river</p>";
				
				$mysql = mysql_connect('localhost', 'root', 'hydref');
				if (!$mysql) 
				{
					die('Could not connect: ' . mysql_error());
				}
					
				$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
				get_info();
				
				function get_info()
				{
					$river = $_GET['search'];
					$riverInfo = mysql_query("select * from riverinfo where name='$river'");	
					
					while($row = mysql_fetch_array($riverInfo))
					{							
						echo "<div id='content'>";
						echo "<p id='grade' class='rText'>Grade: $row[2]</p>";
						echo "<p id='rInfo'>River info: $row[3]</p>";
					}
				}
			?>
		</div>
			<html>
				<head>
					<style>
						#map-canvas {
							clear: both;
							float: right;
							height: 300px;
							padding: 0;
							right: 135px;
							top: 166px;
							width: 20%;
						} 
					</style>
				</head>
				<body>
					<div id="map-canvas">
					</div>
				</body>
			</html>
		
		<script>
			var directionsDisplay;
			var rivLocation;
			var lon, lat;
			var usrlon, usrlat;
			var directionsService = new google.maps.DirectionsService();
			var riv = $('#rTitle').html();
			
			var usrLocation = getUserInfo();
			
			usrlon = usrLocation.split(",")[0];
			usrlat = usrLocation.split(",")[1];
				
			$.post("functions.php", {data: 6, riv: riv}, function(data)
			{
				rivLocation = data;
				lon = rivLocation.split(",")[0];
				lat = rivLocation.split(",")[1];
				console.log(rivLocation);
			});
			
			
			function initialize() {
				var position = new google.maps.LatLng(usrlon,usrlat);
				directionsDisplay = new google.maps.DirectionsRenderer();

				var mapOptions = {
				  center: position,
				  zoom: 9
				};
				
				var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
				
				directionsDisplay.setMap(map);
				calcRoute();
			}
			
			
			function calcRoute() {
			  var start = new google.maps.LatLng(usrlon,usrlat);
			  var end = new google.maps.LatLng(lon,lat);
			  var request = {
				  origin:start,
				  destination:end,
				  travelMode: google.maps.TravelMode.DRIVING
			  };
			  directionsService.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
				  directionsDisplay.setDirections(response);
				}
			  });
			}
			
			$('#rImage').append("<img src='images/"+riv+".jpg' width='400px'/>");
			get_local_weather(riv);
			
			$.getJSON("river.php", function(data)
			{	
				var river;
				var index;
				
				$(data).each(function(i)
				{
					if(data[i].name.toLowerCase() == riv)
					{
						index = i;
					}	
				});
				$(".rivBox").append("<div id='drop"+index+"' class='water-drop'></div><div id='rLine'></div><div class='rBox' id='box"+index+"'></div><div class='bText' id='rName"+index+"'></div><div class='bText' id='rLevel"+index+"'></div><div class='bText' id='rDist"+index+"'></div>");	
				var name = document.getElementById("rName"+index);
				var level = document.getElementById("rLevel"+index);
				var dist = document.getElementById("rDist"+index);
				var drop = document.getElementById("drop"+index);
				var height = Math.min(Math.max(data[index].level.substr(0,4) * 100, 2), 70);
				
				name.innerHTML = data[index].name;
				level.innerHTML = data[index].level;
				dist.innerHTML = data[index].distance;
				
				$("#box"+index).css("height", height);
			});
			
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
		
	</body>
</html>
