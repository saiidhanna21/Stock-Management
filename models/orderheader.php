<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `orderheader` ( `order_id` int NOT NULL AUTO_INCREMENT,
					`order_type` enum('purchase','return')DEFAULT 'purchase' NOT NULL,
					`supplier_id` int not null,
					`order_date` date NOT NULL ,	
					`order_status` 	enum('received', 'pending', 'returned','partially_returned')DEFAULT 'pending' NOT NULL ,						
                    PRIMARY KEY (`order_id`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table orderheader Created";
?>