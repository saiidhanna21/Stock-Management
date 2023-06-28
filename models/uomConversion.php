<?php
include('../utils/connect.php');
$table = "CREATE TABLE IF NOT EXISTS `uomConversion` ( `id` INT NOT NULL AUTO_INCREMENT,
					`item_number` decimal NOT NULL,
                    `from_uom` VARCHAR(20) NOT NULL , 
                    `to_uom` VARCHAR(20) NOT NULL , 
                    `conversion_factor` decimal NOT NULL ,                 
                    PRIMARY KEY (`id`));";
if(!mysqli_query($con,$table)){
        echo "Error creating table: " . mysqli_error($con);
        die;
    }
echo "Table uomConversion Created";
?>