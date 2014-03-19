var x = document.getElementById("location");
var w = document.getElementById("weather");
var f = document.getElementById("hourly");
var d = document.getElementById("daily");
var icon = document.getElementById("icon");
var time = document.getElementById("time");

function run()
{
	getUserInfo();
	getRiverInfo();
	getWeatherInfo();	
	get_location(longi, lat);			
}		

function getUserInfo()
{
	var x = document.cookie.split(";")[0].substr(5);
	$("#user").html("Welcome back "+x);
}
function getWeatherInfo()
{
	//check the geolocation API is available
	if ("geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition(function(position) {
			get_location(position.coords.longitude, position.coords.latitude);
		}).fail(function()
		{
			alert("fail");	
		});
	} else {
			x.innerHTML = "Geolocation not found";
	}	
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
				
				//Get weather file
				$.get("weather/weather.json?nocache="+Math.random(), function(data)
				{
					//Current Weather Info
					w.innerHTML = "Weather: " + data.currently.summary;
					f.innerHTML = "Temp: "+data.currently.temperature +"&deg;C";
					d.innerHTML = "Percip: " + data.currently.precipIntensity;
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
					}
					
					//Check if weather file needs to be updated
					$.post("getWeather.php", {check:true, lonlat: lat + "," + longi}, function(data){});
				}).fail(function()
				{
					// if file doesn't exist do curl request
					$.post("getWeather.php", {check:false, lonlat: lat + "," + longi}, function(data){
						//Then get the json file to display weather						
						$.get("weather/weather.json", function(data){
							//Current Weather Info
							w.innerHTML = data.currently.summary;
						});
					});
				});	
			});
}

function getRiverInfo()
{
	var left = 5;
	//-----------------Get River Info!!-----------------
	$.getJSON("river.php", function(data)
	{
		$(data).each(function(index){
			$( "#favriv" ).append( "<div class='rivBox' id='"+index+"'></div>" );
			$("#"+index).append("<div id='rLine'></div><div class='rBox' id='rBox"+index+"'></div><div id='rName"+index+"'></div><div id='rLevel"+index+"'></div><div id='rDist"+index+"'></div>");	
			
			var name = document.getElementById("rName"+index);
			var level = document.getElementById("rLevel"+index);
			var dist = document.getElementById("rDist"+index);
			
			name.innerHTML = data[index].name;
			level.innerHTML = data[index].level;
			dist.innerHTML = data[index].distance;
			$("#"+index).css("left", left+"px");
			left = left + 180;
		});
	});
}	
				
