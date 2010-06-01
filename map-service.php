<?php
	$ip = $_SERVER['REMOTE_ADDR'];

	// List points from database
	if ($_GET['action'] == 'listpoints') {
		$query = "SELECT * FROM locations WHERE ip='$ip'";
		$result = map_query($query);
		$points = array();
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			array_push($points, array('name' => $row['name'], 'lat' => $row['lat'], 'lng' => $row['lng']));
		}
		echo json_encode(array("Locations" => $points));
		exit;
	}
	
	// Save a point from our form
	if ($_POST['action'] == 'savepoint') {
		$name = $_POST['name'];
		if(preg_match('/[^\w\s]/i', $name)) {
			fail('Invalid name provided.');
		}
		if(empty($name)) {
			fail('Please enter a name.');
		}
		
		// Query
		$query = "INSERT INTO locations SET name='$_POST[name]', lat='$_POST[lat]', lng='$_POST[lng]', ip='$ip'";
		$result = map_query($query);
		
		if ($result) {
			success(array('lat' => $_POST['lat'], 'lng' => $_POST['lng'], 'name' => $name));
		} else {
			fail('Failed to add point.');
		}
		exit;
	}
	
	function map_query($query) {
		// Connect
		mysql_connect('mysql_host', 'mysql_user', 'mysql_password')
		    OR die(fail('Could not connect to database.'));
		
		mysql_select_db('mysql_database');
		return mysql_query($query);
	}
	
	function fail($message) {
		die(json_encode(array('status' => 'fail', 'message' => $message)));
	}
	
	function success($data) {
		die(json_encode(array('status' => 'success', 'data' => $data)));
	}
?>