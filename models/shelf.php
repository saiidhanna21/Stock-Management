<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `shelf` ( `shelf_num` int NOT NULL,
					`shelf_status` ENUM('occupied','free')DEFAULT 'free' NOT NULL,
                    PRIMARY KEY (`shelf_num`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table shelf Created";
?>