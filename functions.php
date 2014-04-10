<?php
ob_start();
$data = $_POST['data'];
$user = $_POST['user'];
$pass = $_POST['pass'];
$riv = $_POST['riv'];
$loc =  $_POST['location'];

$mysql = mysql_connect('localhost', 'root', 'hydref');
if (!$mysql) 
{
	die('Could not connect: ' . mysql_error());
}
	
$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");


switch ($data)
{
	case 0:
		log_in($user, $pass);
		break;
	case 1:
		create_user($user,$pass,$loc);
		break;
	case 2:
		delete_session();
		break;
	case 3: 
		getInfo();
		break;	
	case 4: 
		get_rivers();
		break;
	case 5:
		update_location($loc, $user);
		break;
	case 6:
		get_river_loc($riv);
		break;
	case 7:
		update_fav($riv);
		break;
	case 8:
		remove_fav($riv);
		break;
}
				
function log_in($user, $pass)
{
	$userInfo = mysql_query("select * from users where name = '$user'");
	
	while($row = mysql_fetch_array($userInfo))
	{
		if(strcmp($row[1], $pass) != 0)
		{
			echo "User name or password incorrect, please try again";
		}
		else
		{
			setcookie("user", $user, time()+60*60*24*30);
			setcookie("longlat", $row[3], time()+60*60*24*30);
			echo "Logged in!";
			header("Location: index.html");
			Exit;
		}
	}	
}

function update_fav($riv)
{
	$user = $_COOKIE['user'];	
	$river = mysql_fetch_array(mysql_query("select stationID from rivers where name='$riv'"));
	$query = mysql_query("INSERT INTO `userRivers` (`user_name`, `river_id`) values ('$user', '$river[0]')");
}

function remove_fav($riv)
{
	$user = $_COOKIE['user'];	
	$river = mysql_fetch_array(mysql_query("select stationID from rivers where name='$riv'"));
	$query = mysql_query("delete from userRivers where user_name='$user' and river_id = '$river[0]'");
}

function get_river_loc($user)
{
	$riverLoc = mysql_query("select longlat from rivers where name = '$user'");	
	$loc = mysql_fetch_array($riverLoc);	
	echo $loc[0];
}

function delete_session()
{
	setcookie("user", $user, time()-60*60*24*30);
	setcookie("longlat", $row[3], time()-60*60*24*30);
	header("Location: login.html");
}

function update_location($loc, $user)
{
	$update = mysql_query("update users set location='$loc' where name ='$user'");
	
	echo $update;
}
function get_rivers()
{
	$rivStr ="";
	$rivers = mysql_query("select name from rivers");
	
	while($row = mysql_fetch_array($rivers))
	{
		if(!empty($row[0]))
		{
			echo $row[0] . ",";
		}
	}	
}
	
function create_user($userID, $pass, $location)
{	
	$createUser = mysql_query("insert into users (name, password, rivers, location) values ('$userID', '$pass', null, '$location')") or die("Couldn't submit");
	setcookie("user", $userID, time()+60*60*24*30);
	setcookie("longlat", $location, time()+60*60*24*30);
	
	header("Location: index.html");
	Exit;
}
function delete_user($userID)
{
	connect();
	
	$removeUser = mysql_query("delete from users where name = '$userID'");
	
	echo $removeUser;
	
	mysql_close($mysql);
}

function ammend($userID, $item, $record)
{
	connect();
	
	switch ($record)
	{
		case 0:
			$changeLoc = mysql_query("update users set location = '$item' where name = '$userID'");
			break;
		case 1:
			$changeRiv = mysql_query("update users set rivers = '$item' where name = '$userID'");
			break;
	}
	
	echo $changeLoc;
	
	mysql_close($mysql);
}
mysql_close($mysql);
