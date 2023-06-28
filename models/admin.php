<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `admin` ( `admin_id` int NOT NULL AUTO_INCREMENT,
					`username` VARCHAR(20) not null,
					`password` VARCHAR(70) NOT NULL,
					`email` VARCHAR(30) not null,
                    PRIMARY KEY (`admin_id`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table admin Created";
?>