<?php

	/*
	 * Minecraft TNT Airballoon
	 * A front-end for steering a TNT airship in-game. 
	 * The idea was all along to modify an airships plugin as a back-end to read a database and move accordingly but this never happened.
	 *
	 * Written by Crizzly, Stenudd
	 * Website: http://crizzly.fi (demo available)
	 *
	 * RIGHT, LEFT click updates the X axis with +1 respectively -1 
	 * UP, DOWN click updates the Y axis with +1 respectively -1 
     * airballoon.php calls update.php every second to update the balloon on the map in "realtime" 
	 *
	 * !!You need to insert the database inside update.php aswell for the "realtime" updating to work.!!
	 *
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
	$checklocation = "SELECT `loc_x`, `loc_z` FROM `balloon_loc` WHERE id = 1;";
	$sql = "CREATE TABLE `balloon_loc` ( `id` INT(10) NOT NULL , `loc_x` INT(10) NOT NULL , `loc_z` INT(10) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
	
	$conn->query($sql);
	
	$location_x_bl = 0;
	$location_z_bl = 0;
	
	$location_x = 0;
	$location_z = 0;
	
	$result_bl = $conn->query($checklocation_bl);
	$result = $conn->query($checklocation);
	
	if ($result_bl->num_rows > 0) {
		while($row = $result_bl->fetch_assoc()) {
			$location_x_bl = $row["loc_x"];
			$location_z_bl = $row["loc_z"];
		}
	} else {
    echo "Cordinates not found";
}

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$location_x = $row["loc_x"];
			$location_z = $row["loc_z"];
		}
	} else {
    echo "Cordinates not found";
}

	$update_x = "+ 0";
	$update_z = "+ 0";
	$update_x_bl = "+ 0";
	$update_x_bl = "+ 0";

	if(isset($_POST['update_up']))	{
		if($location_z > -192){
			$update_z = "- 1";
			$update_z_bl = "- 1";
		}
		else{
			$update_z = "- 0";
			$update_z_bl = "- 0";
		}
	}
	else if(isset($_POST['update_right'])){
			if($location_x < 192){
			$update_x = "+ 1";
			$update_x_bl = "+ 1";
		}
		else{
			$update_x = "+ 0";
			$update_x_bl = "+ 0";
		}
	}
	else if(isset($_POST['update_left'])){
		if($location_x > -192){
			$update_x = "- 1";
			$update_x_bl = "-1";
		}
		else{
			$update_x = "- 0";
			$update_x_bl = "-0";
			
		}
	}
	else if (isset($_POST['update_down'])){
		if($location_z < 192){
			$update_z = "+ 1";
			$update_z_bl = "+ 1";
		}
		else{
			$update_z = "+ 0";
			$update_z_bl = "+ 0";
		}
	}
	else{
		//echo "not updated";
	}

	$set = "SET @id = 1, @loc_x = 0, @loc_z = 0;";
	$update = 'INSERT INTO balloon_loc (id, loc_x, loc_z)
		VALUES
			(@id, @loc_x, @loc_z)
		ON DUPLICATE KEY UPDATE
			loc_x = loc_x ' . $update_x . ',
			loc_z = loc_z ' . $update_z . ';';
	
	$set_bl = "SET @id = 2, @loc_x = 182, @loc_z = 182;";
	$update_bl = 'INSERT INTO balloon_loc (id, loc_x, loc_z)
		VALUES
			(@id, @loc_x, @loc_z)
		ON DUPLICATE KEY UPDATE
			loc_x = loc_x ' . $update_x_bl . ',
			loc_z = loc_z ' . $update_z_bl . ';';

	$conn->query($set);
	$conn->query($update);
	
	$conn->query($set_bl);
	$conn->query($update_bl);
	
	$conn->close();
?> 

<!DOCTYPE html>

<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style>
	canvas {
		border:2px solid black;
        position:absolute;
        top: 0;
        left: 0;
        z-index: 2000;     
}
    .up
    {
        position:absolute;
        top:0;
        left: 170px;
        z-index:3000;
    }
	.left
    {
        position:absolute;
        top:170px;
        left:2px;
        z-index:3000;
    }
	.right
    {
        position:absolute;
        top:170px;
        left:342px;
        z-index:3000;
    }
	.down
    {
        position:absolute;
        top:349px;
        left:170px;
        z-index:3000;
    }
	h1{
		font-size: 20px;
		font-family: Arial;
		margin-left: 5%;
		margin-top: 2%;
	}
       
</style>
</head>
<script>
var xloc;
var zloc;
var tntBalloon;
var myBackground;

updatedata();

function updatedata(){
	$.getJSON('update.php', function (data) {
		xloc = data.x;
		zloc = data.z;
		tntBalloon.x = xloc;
		tntBalloon.y = zloc;
		tntBalloon.update();
	});
}

setInterval(function(){
	updatedata();
}, 1000);

function startMap() {	
    tntBalloon = new component(30, 30, "balloon.gif", xloc, zloc, "image");
	myBackground = new component(384, 384, "map.jpg", 0, 0, "image");
    MapOverview.start();
	console.log('started');
	console.log(tntBalloon);
	console.log(xloc);
	console.log(zloc);
}

var MapOverview = {
    canvas : document.createElement("canvas"),
    start : function() {
        this.canvas.width = 384;
        this.canvas.height = 384;
        this.context = this.canvas.getContext("2d");
        document.body.insertBefore(this.canvas, document.body.childNodes[0]);
        this.frameNo = 0;
        this.interval = setInterval(updateMapArea, 20);
        },
    clear : function() {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    },
    stop : function() {
        clearInterval(this.interval);
    }
}

function component(width, height, color, x, y, type) {
    this.type = type;
    if (type == "image") {
        this.image = new Image();
        this.image.src = color;
    }
    this.width = width;
    this.height = height;
    this.speedX = 0;
    this.speedY = 0;  
    this.x = x;
    this.y = y;    
    this.update = function() {
        ctx = MapOverview.context;
        if (type == "image") {
            ctx.drawImage(this.image, 
                this.x, 
                this.y,
                this.width, this.height);
        } else {
            ctx.fillStyle = color;
            ctx.fillRect(this.x, this.y, this.width, this.height);
        }
    }
    this.newPos = function() {
        this.x += this.speedX;
        this.y += this.speedY;  
    }    
}

function updateMapArea() {
    MapOverview.clear();
	myBackground.newPos(); 
	myBackground.update();
    tntBalloon.newPos(); 	
    tntBalloon.update();
	clearmove();
}

function moveup() {
	if(tntBalloon.y > 0){
	tntBalloon.speedY = -1;
	$.post("airballoon.php", {'update_up':''}, function (data) {});
	}
	else{
		tntBalloon.speedY = 0;
	}
}

function movedown() {
	if(tntBalloon.y != 355){		
    tntBalloon.speedY = 1;	
	$.post("airballoon.php", {'update_down':''}, function (data) {});
	}
}

function moveleft() {
	if(tntBalloon.x > 0){
    tntBalloon.speedX = -1; 
	$.post("airballoon.php", {'update_left':''}, function (data) {});
	}
}

function moveright() {
	if(tntBalloon.x != 355){
    tntBalloon.speedX = 1;
	$.post("airballoon.php", {'update_right':''}, function (data) {});
	}
}

function clearmove() {
    tntBalloon.speedX = 0; 
    tntBalloon.speedY = 0; 
}

$(function() {
    $("#dirbutton").click(function() {
        $("#dirbutton").attr("disabled", "disabled");
        setTimeout(function() {
            $("#dirbutton").removeAttr("disabled");      
        }, 3000);
    });
});

$(function() {
    $("#dirbutton2").click(function() {
        $("#dirbutton2").attr("disabled", "disabled");
        setTimeout(function() {
            $("#dirbutton2").removeAttr("disabled");      
        }, 3000);
    });
});

$(function() {
    $("#dirbutton3").click(function() {
        $("#dirbutton3").attr("disabled", "disabled");
        setTimeout(function() {
            $("#dirbutton3").removeAttr("disabled");      
        }, 3000);
    });
});

$(function() {
    $("#dirbutton4").click(function() {
        $("#dirbutton4").attr("disabled", "disabled");
        setTimeout(function() {
            $("#dirbutton4").removeAttr("disabled");      
        }, 3000);
    });
});
</script>
<body onload="startMap()">
<title>Minecraft TNT Airballoon</title>
  <button id="dirbutton" name="update_up" class="up" onclick="moveup()"><img src="up.png" style="width:30px; height:30px;"></img></button>
  <button id="dirbutton2" name="update_left" class="left" onclick="moveleft()"><img src="left.png" style="width:30px; height:30px;"></img></button>
  <button id="dirbutton3" name="update_right" class="right" onclick="moveright()"><img src="right.png" style="width:30px; height:30px;"></img></button>
  <button id="dirbutton4" name="update_down" class="down" onclick="movedown()"><img src="down.png" style="width:30px; height:30px;"></img></button>
 </body>
</html> 