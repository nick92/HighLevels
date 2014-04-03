var x = document.getElementById("location");
var w = document.getElementById("weather");
var f = document.getElementById("hourly");
var d = document.getElementById("daily");
var icon = document.getElementById("icon");
var time = document.getElementById("time");
var fWeather = document.getElementById("future-weather");
var longi, lat, filter;

function run()
{
	getUserInfo();
	getRiverInfo();
	get_updated_time();
	future_weather();		
	getWeatherInfo();	
	get_location(longi, lat);	
}		

function get_updated_time()
{
	var timeNow = new Date().getTime();
	var weatherTime;
	
	$.get("weather/weather.json?nocache="+Math.random(), function(data)
	{
		weatherTime = data.currently.time;
		var diff = (timeNow - weatherTime);
		console.log(diff);
	});
}
function getUserInfo()
{
	//Gather cookie inforation and then break it into array
	var cooke = document.cookie.split(";");
	var x = cooke[0].substr(5);
	$("#user").html("Welcome back "+x+" <a href='login.html' id='logout'> Logout</a>");
		$('#logout').click(function(){
			$.post('functions.php', {data:2});
		});
	lat = cooke[1].substr(9).split("%2C")[0];
	longi = cooke[1].substr(9).split("%2C")[1];
}
function getWeatherInfo()
{
	//Download the current json file before is updated
	get_weather_json();	
	//check the geolocation API is available
	if ("geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition(function(position) {
			get_location(position.coords.longitude, position.coords.latitude);
			if(position.coords.longitude !== parseFloat(longi))
			{
				$('#update').show();
				setTimeout(function(){$('#update').hide()}, 10000);
			}
		}).fail(function()
		{
			alert("fail");	
		});
	} else {
			x.innerHTML = "Geolocation not found";
	}	
}

function get_local_weather(riv)
{
	$.post("functions.php", {data: 6, riv: riv}, function(data)
	{
		$.post("getWeather.php", {check:'false', lonlat: data}, function(data){
			$.get("weather/weather.json?nocache="+Math.random(), function(data)
			{
				//Current Weather Info
				w.innerHTML = "Weather: " + data.currently.summary;
				f.innerHTML = "Temp: "+data.currently.temperature.toFixed(0) +"<p style='font-size: 10px; display:inline;'>&deg;C</p>";
				d.innerHTML = "Percip: " + data.currently.precipIntensity.toFixed(2)+"<p style='font-size: 10px; display:inline;'> mm/hr </p>";
				wicon = data.currently.icon;
				
				switch(wicon)
				{
					case 'wind':
						icon.innerHTML = "<img src='icon/sun.rays.cloud.png' />";
						break;
					case 'rain':
						icon.innerHTML = "<img src='icon/cloud.drizzle.png' />";
						break;
					case 'sunny':
						icon.innerHTML = "<img src='icon/sun.rays.small.png' />";
						break;
					case 'partly-cloudy-day':
						icon.innerHTML = "<img src='icon/cloud.png' />";
						break;
				}
			});
		});
	});
}
$('#refresh').click(function(){
	$.post("getWeather.php", {check:'false', lonlat: lat + "," + longi}, function(data){
			get_weather_json();	
	});
});

$('#near').click(function(){
	var id = $( this ).attr('id');
	$('#neariv').html("");
	$('#favourites').html("");
	getRiverInfo(0);
});
$('#high').click(function(){
	var id = $( this ).attr('id');
	$('#neariv').html("");
	$('#favourites').html("");
	getRiverInfo(1);
});						
function update_location()
{
	var clat;
	var clong;
	var cooke = document.cookie.split(";");
	var name = cooke[0].substr(5);
	
	if ("geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition(function(position) {
			clong = position.coords.longitude;
			clat = position.coords.latitude;
			$.post("functions.php", {data: 5, location:clat+","+clong, user:name}, function(data)
			{
				if(data == 1)
				{
					$('#update').hide();
				}
			});
		});
	}		
}
function future_weather()
{
	var left = 10;
	var j = 0;
	var days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; 
	$.get("weather/weather.json?nocache="+Math.random(), function(data)
	{
		for(var i = 0; i < 5; i++)
			{
				$("#future-weather").append("<div class='fwbox' id='fbox"+i+"'></div>");
				$('#fbox'+i).append("<div id='ficon"+i+"'></div><h3 id='day"+i+"' class='days'></h3><div id='fWeather"+i+"'></div><div id='fPerc"+i+"'></div><div id='fTemp"+i+"'></div>");
				var date = new Date().getDay() + i;
				
				if(date > 6)
				{
					date = j++;
				}
				
				$('#day'+i).html(days[date]);
				var weather = document.getElementById("fWeather"+i);
				var perc = document.getElementById("fPerc"+i);
				var temp = document.getElementById("fTemp"+i);
				var ficon = document.getElementById("ficon"+i);
				var percip = data.daily.data[i].precipIntensity;
				var wicon = data.daily.data[i].icon;
				
				
				switch(wicon)
				{
					case 'wind':
						ficon.innerHTML = "<img src='icon/sun.rays.cloud.png' class='fwi' width='25px'/>";
						break;
					case 'rain':
						ficon.innerHTML = "<img src='icon/cloud.drizzle.png' class='fwi' width='25px'/>";
						break;
					case 'sunny':
						ficon.innerHTML = "<img src='icon/sun.rays.small.png' class='fwi' width='25px'/>";
						break;
					case 'partly-cloudy-day':
						ficon.innerHTML = "<img src='icon/cloud.png' class='fwi' width='25px'/>";
						break;
					case 'cloudy':
						ficon.innerHTML = "<img src='icon/cloud.png' class='fwi' width='25px'/>";
						break;
				}
				
				weather.innerHTML = "Weather: " + data.daily.data[i].icon;
				perc.innerHTML = "Percep: " + percip.toFixed(2) + "<p style='font-size: 8px; display:inline;'> mm/hr</p>";
				temp.innerHTML = "Temp: " + data.daily.data[i].temperatureMin.toFixed(0) + "<p style='font-size: 8px; display:inline;'>&deg;C</p>";
				
				$("#fbox"+i).css("left", left+"px");
				left = left + 140;
			}
	});
}

function get_weather_json()
{
	//Get weather file
	$.get("weather/weather.json?nocache="+Math.random(), function(data)
	{
		//Current Weather Info
		w.innerHTML = "Weather: " + data.currently.summary;
		f.innerHTML = "Temp: "+data.currently.temperature.toFixed(0) +"<p style='font-size: 10px; display:inline;'>&deg;C</p>";
		d.innerHTML = "Percip: " + data.currently.precipIntensity.toFixed(2)+"<p style='font-size: 10px; display:inline;'> mm/hr </p>";
		wicon = data.currently.icon;
		
		switch(wicon)
		{
			case 'wind':
				icon.innerHTML = "<img src='icon/sun.rays.cloud.png' />";
				break;
			case 'rain':
				icon.innerHTML = "<img src='icon/cloud.drizzle.png' />";
				break;
			case 'sunny':
				icon.innerHTML = "<img src='icon/sun.rays.small.png' />";
				break;
			case 'partly-cloudy-day':
				icon.innerHTML = "<img src='icon/cloud.png' />";
				break;
			case 'cloudy':
				icon.innerHTML = "<img src='icon/cloud.png' />";
				break;
		}
		$.post("getWeather.php", {check:'true', lonlat: lat + "," + longi}, function(data){});
		
		}).fail(function()
		{
			// if file doesn't exist do curl request
			$.post("getWeather.php", {check:'false', lonlat: lat + "," + longi}, function(data){
				//Then get the json file to display weather						
				$.get("weather/weather.json", function(data){
					//Current Weather Info
					w.innerHTML = data.currently.summary;
				});
			});
		});
}	
	
function get_location(longi, lat)
{
	var location;
	var wicon;
	
	//Get users location JSON file from Google
	$.get("http://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + longi +"&sensor=false", function(data)
	{
		//display location
		//x.innerHTML = "Your current location is: "+data.results[0].address_components[1].long_name;
		location = data.results[0].address_components[1].long_name; 			
		get_weather_json();
	});
}

function getRiverInfo(filter)
{
	var fl = 5;
	var nl = 5;
	//-----------------Get River Info!!-----------------
	$.getJSON("river.php", {type: filter}, function(data)
	{
		$(data).each(function(index){
			
			if(data[index].fav == "1")
			{
				$( "#favourites" ).append( "<div class='rivBox' id='"+index+"'></div>" );
				$("#"+index).append("<div id='drop"+index+"' attr='"+data[index].name+"' class='water-drop'></div><div id='rLine'></div><div class='rBox' id='box"+index+"'></div><div class='bText' id='rName"+index+"'></div><div class='bText' id='rLevel"+index+"'></div><div class='bText' id='rDist"+index+"'></div>");	
				var drop = document.getElementById("drop"+index);
				drop.innerHTML = "<img src='images/water-drop-full.png' attr='1' id='water-droplet"+index+"'/>";
				$("#"+index).css("left", fl+"px");
				fl = fl + 180;
				if(fl > 1000)
				{
					$('#arrowFL').show();
					$('#arrowFR').show();
				}
			}
			else
			{
				$( "#neariv" ).append( "<div class='rivBox' id='"+index+"'></div>" );
				$("#"+index).append("<div id='drop"+index+"' attr='"+data[index].name+"' class='water-drop'></div><div id='rLine'></div><div class='rBox' id='box"+index+"'></div><div class='bText' id='rName"+index+"'></div><div class='bText' id='rLevel"+index+"'></div><div class='bText' id='rDist"+index+"'></div>");	
				var drop = document.getElementById("drop"+index);
				drop.innerHTML = "<img src='images/water-drop-empty.png' attr='0' id='water-droplet"+index+"'/>";
				$("#"+index).css("left", nl+"px");
				nl = nl + 180;
				if(nl > 1000)
				{
					$('#arrowL').show();
					$('#arrowR').show();
				}
			}		
			
			$(".water-drop").mouseenter(function(){
					var attr = $(this).find('img').attr('attr');
					var id = $(this).find('img').attr('id');
					if(attr == 0)
					{
						$('#'+id).attr('src','images/water-drop-full.png');
					}
					else
					{
						$('#'+id).attr('src','images/water-drop-empty.png');
					}
					console.log("enter");
				});
			$('.water-drop').mouseleave(function(){
					var id = $(this).find('img').attr('id');
					var attr = $(this).find('img').attr('attr');
					
					if(attr == 0)
					{
						$('#'+id).attr('src','images/water-drop-empty.png');
					}
					else
					{
						$('#'+id).attr('src','images/water-drop-full.png');
					}
					console.log("leave");
			});
			
			var name = document.getElementById("rName"+index);
			var level = document.getElementById("rLevel"+index);
			var dist = document.getElementById("rDist"+index);
			var height = Math.min(Math.max(data[index].level.substr(0,4) * 100, 2), 70);
			
			name.innerHTML = data[index].name;
			level.innerHTML = data[index].level;
			dist.innerHTML = data[index].distance;
			
			$("#box"+index).css("height", height);
		});
		
		$('.water-drop').bind('click', function(){
			var	id = $(this).attr('attr');
			var attr = $(this).find('img').attr('attr');
			console.log(id);
			if(attr == 0)
			{
				$.post('functions.php', {data: 7, riv: id}, function(data){console.log(data)});
			}
			else
			{
				$.post('functions.php', {data: 8, riv: id}, function(data){console.log(data)});
			}
			location.reload(); 
		});
	});		
}	
				
