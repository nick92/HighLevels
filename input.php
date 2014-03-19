<html>
	<?php
		$name = $_GET['name'];
		$stationID = $_GET['stationID'];
		$town = $_GET['town'];
		$longlat = $_GET['longlat'];
		
		$mysql = mysql_connect('localhost', 'root', 'hydref');
		$selected = mysql_select_db("highlevels",$mysql) or die("Could not select examples");
		$input = mysql_query("insert into rivers (name, stationID, town, longlat) values ('$name','$stationID','$town','$longlat')") or die ("Couldn't do it".mysql_error());
		echo $input;
	?>
	<body>
		<form action="" id="input-form" method="post">
			<fieldset>
				<input type="text" id="name" name="name" />
				<input type="text" id="stationID" name="stationID" />
				<input type="text" id="town" name="town" />
				<input type="text" id="longlat" name="longlat" />
				<input type="submit" id="search-submit" value="Sub" />
			</fieldset>
		</form>
	</body>
</html>
