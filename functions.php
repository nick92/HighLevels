<?php
function connect()
{
	$mysql = mysql_connect('localhost', 'root', 'hydref');
	if (!$mysql) 
	{
		die('Could not connect: ' . mysql_error());
	}
		
	$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
}
function log_in()
{
	connect();
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	
	echo $user.$info;
	$userInfo = mysql_query("select * from users where name = '$user'");
	
	while($row = mysql_fetch_array($userInfo))
	{
		if(strcmp($row[1], $pass) != 0)
		{
			echo "User name or password incorrect, please try again";
		}
		else
		{
			echo "Logged in!";
		}
	}	
	
	mysql_close($mysql);
}
function create_user($userID, $pass, $location)
{
	connect();
	
	$createUser = mysql_query("insert into users (name, password, rivers, location) values ('$userID', '$pass', null, '$location')");
	
	echo $createUser;
	
	mysql_close($mysql);
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
create_user($_POST['user'], $_POST['pass'], $_POST['location']);
