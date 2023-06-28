<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `Supplier` ( `supplier_id` int NOT NULL AUTO_INCREMENT,
					`supplier_name` VARCHAR(20) NOT NULL ,
					`supplier_location` VARCHAR(150) NOT NULL ,					
                    PRIMARY KEY (`supplier_id`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table Supplier Created";
?>