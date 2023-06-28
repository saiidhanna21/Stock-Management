<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `orderdata` ( `order_id` int NOT NULL ,
					`order_line` int NOT NULL,
					`item_number` decimal not null,
					`unit_quantity` int NOT NULL ,	
					`unit_uom` VARCHAR(20) NOT NULL,
					`unit_price` decimal not null,
					`total_unit_amount` decimal NOT NULL ,
					`primary_unit_price` decimal NOT NULL ,
					`primary_quantity` decimal NOT NULL ,
					`item_expiry_date` date not null,
					`order_line_status` enum('accepted','returned')DEFAULT 'accepted' NOT NULL ,
                    PRIMARY KEY (`order_id`,`order_line`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table orderdata Created";
?>