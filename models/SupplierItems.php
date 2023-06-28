<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `SupplierItems` (`supplier_id` int NOT NULL,
					`item_number` decimal not null,
					`pricing_amount` VARCHAR(20) NOT NULL ,
					`item_expiry_date` date NOT NULL ,	
					`produced_quantity` int NOT NULL ,						
                    PRIMARY KEY (`supplier_id`,`item_number`,`item_expiry_date`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table SupplierItems Created";
?>