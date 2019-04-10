<?php

	/*
	 * Minecraft TNT Airship
	 * A front-end for steering a TNT airship in-game. 
	 * The idea was all along to modify an airships plugin as a back-end to read a database and move accordingly but this never happened.
	 *
	 * Written by Crizzly, Stenudd
	 * Website: http://crizzly.fi (demo available)
	 */

	$servername = "host";
	$username = "user";
	$password = "password";
	$dbname = "dbname";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	
	$checklocation_bl = "SELECT `loc_x`, `loc_z` FROM `balloon_loc` WHERE id = 2;";
	
	$location_x_bl = 0;
	$location_z_bl = 0;
	
	$result_bl = $conn->query($checklocation_bl);
	
	if ($result_bl->num_rows > 0) {
		while($row = $result_bl->fetch_assoc()) {
			$location_x_bl = $row["loc_x"];
			$location_z_bl = $row["loc_z"];
		}
	} else {
    echo "Cordinates not found";
}
	echo json_encode(array(
	"x"=>$location_x_bl,
	"z"=>$location_z_bl
),JSON_NUMERIC_CHECK);

	$conn->close;
?>