<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `ItemMaster` ( `item_number` decimal NOT NULL,
                    `uom_purchasing` VARCHAR(20) NOT NULL , 
                    `uom_pricing` VARCHAR(20) NOT NULL , 
                    `uom_selling` VARCHAR(20) NOT NULL ,
					`item_name` VARCHAR(20) NOT NULL ,
					`item_image` VARCHAR(150) NOT NULL ,
                    `item_description` text NOT NULL ,				
                    PRIMARY KEY (`item_number`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table ItemMaster Created";
?>