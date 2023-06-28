<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `stockdetails` ( `item_number` decimal NOT NULL ,
					`item_expiry_date` date not null,
					`shelf_num` int NOT NULL,
					`primary_quantity_batch` decimal not null,
					`primary_unit_price` decimal not null,
                    PRIMARY KEY (`item_number`,`item_expiry_date`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table stockdetails Created";
?>