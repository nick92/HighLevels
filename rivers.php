<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css"> 
		<link href='http://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>o
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<title>River information on a page</title>
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
						echo "<p id='grade'>Grade: $row[2]</p>";
						echo "<p id='rInfo'>River info: $row[3]</p>";
					}
				}
			?>
		</div>
		<script>
			var riv = $('#rTitle').html();
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
			get_weather_json();
		</script>
		
	</body>
</html>
