<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `stock` ( `item_number` decimal NOT NULL,
					`primary_quantity` decimal not null,
                    `primary_initial_quantity` decimal not null,
					`threshold` int NOT NULL ,	
					`flag` boolean NOT NULL ,						
                    PRIMARY KEY (`item_number`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table stock Created";
?>