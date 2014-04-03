<html>
	<?php
		$name = $_POST['name'];
		$stationID = $_POST['stationID'];
		$town = $_POST['town'];
		$longlat = $_POST['longlat'];
		$data = $_POST['data'];
		$grade = $_POST['grade'];
		$info = mysql_real_escape_string($_POST['info']);
		$date = time();
		
		$mysql = mysql_connect('localhost', 'root', 'hydref');
		$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
		
		switch($data)
		{
			case 0:
				$input = mysql_query("insert into rivers (name, stationID, town, longlat) values ('$name','$stationID','$town','$longlat')") or die ("Couldn't do it".mysql_error());
				break;
			case 1:
				$input = mysql_query("insert into riverinfo (name, date, grade, info) values ('$name','$date','$grade','$info')") or die ("Couldn't do it".mysql_error());
				break;
		}
	?>
	<body>
		<form action="" id="input-form" method="post">
			<fieldset>
				<input type="text" id="name" name="name" value="Name"/>
				<input type="text" id="stationID" name="stationID" value="StationID"/>
				<input type="text" id="town" name="town" value="Town"/>
				<input type="text" id="longlat" name="longlat" value="longlat"/>
				<input type="hidden" id="data" name="data" value="0" />
				<input type="submit" id="search-submit" value="Sub" />
			</fieldset>
		</form>
		<form action="" id="input-form2" method="post">
			<fieldset>
				<input type="text" id="name" name="name" value="Name"/>
				<input type="text" id="grade" name="grade" value="Grade"/>
				<input type="text" id="info" name="info" value="info"/>
				<input type="hidden" id="data" value="1" name="data"/>
				<input type="submit" id="search-submit" value="Sub" />
			</fieldset>
		</form>
	</body>
</html>
