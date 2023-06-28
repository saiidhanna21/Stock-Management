<?php
function receiveOrder($con, $orderID)
{
    //Check if order already received
    $sql = "SELECT `order_status` FROM `orderheader` WHERE `order_id`=$orderID;";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['order_status'] == 'received') {
            flash('Order already received', true);
            return;
        }
    } else {
        flash('Order Not Found', true);
        return;
    }
    $sql = "UPDATE `orderheader` SET `order_status` = 'received' WHERE `order_id` = $orderID";
    mysqli_query($con, $sql);
    $sql = "SELECT * FROM `orderdata` WHERE `order_id` = $orderID";
    $result1 = mysqli_query($con, $sql);
    $row1 = mysqli_fetch_assoc($result1);
    do {
        $itemNumber = $row1['item_number'];
        $primaryQuantity = $row1['primary_quantity'];
        $expiry_date = $row1['item_expiry_date'];
        $primary_unit_price = $row1['primary_unit_price'];
        $sql = "SELECT `primary_quantity` FROM `stock` WHERE `item_number`= $itemNumber";
        $result2 = mysqli_query($con, $sql);
        //Update qty if stock already exists
        if (mysqli_num_rows($result2) > 0) {
            $row2 = mysqli_fetch_assoc($result2);
            $qty = $row2['primary_quantity'] + $primaryQuantity;
            $sql = "UPDATE `stock` SET `primary_quantity`=$qty,`primary_initial_quantity`=$qty, `flag`=1 WHERE `item_number`=$itemNumber";
            mysqli_query($con, $sql);
        } else {
            //INSERT into stock if stock doesn't exists
            //Treshold default 10
            $sql = "INSERT INTO `stock`(`item_number`, `primary_quantity`, `threshold`, `flag`,`primary_initial_quantity`) VALUES ($itemNumber,$primaryQuantity,10,1,$primaryQuantity)";
            mysqli_query($con, $sql);
        }

        //Insert into stock details
        $sql = "SELECT `primary_quantity_batch` FROM `stockdetails` WHERE `item_number`=$itemNumber AND `item_expiry_date`='$expiry_date'";
        $result3 = mysqli_query($con, $sql);

        //Update stock details if same expiry date
        if (mysqli_num_rows($result3) > 0) {
            $row = mysqli_fetch_assoc($result3);
            $qty = $row['primary_quantity_batch'] + $primaryQuantity;
            $sql = "UPDATE `stockdetails` SET `primary_quantity_batch`=$qty WHERE `item_number`=$itemNumber AND `item_expiry_date`='$expiry_date'";
            mysqli_query($con, $sql);
            //Insert into stock details if different expiry date but item already exists
        } else {
            $sql = "SELECT `shelf_num` FROM `stockdetails` WHERE `item_number`=$itemNumber";
            $result4 = mysqli_query($con, $sql);
            if (mysqli_num_rows($result4) > 0) {
                $row = mysqli_fetch_assoc($result4);
                $shelf = $row['shelf_num'];
                $qty = $primaryQuantity;
                $sql = "INSERT INTO `stockdetails`(`item_number`, `item_expiry_date`, `shelf_num`, `primary_quantity_batch`, `primary_unit_price`) 
                VALUES ('$itemNumber','$expiry_date','$shelf','$qty','$primary_unit_price')";
                mysqli_query($con, $sql);
                //Insert into stock details if different expiry date and item doesn't exists
            } else {
                $sql = "SELECT MAX(`shelf_num`) AS max FROM `stockdetails`";
                $result = mysqli_query($con, $sql);
                $shelf = 1;
                if (!is_null($result)) {
                    $row = mysqli_fetch_assoc($result);
                    $shelf = $row['max'] + 1;
                }
                $sql = "INSERT INTO `stockdetails`(`item_number`, `item_expiry_date`, `shelf_num`, `primary_quantity_batch`, `primary_unit_price`) 
                VALUES ('$itemNumber','$expiry_date','$shelf','$primaryQuantity','$primary_unit_price')";
                mysqli_query($con, $sql);
            }
        }
    } while ($row1 = mysqli_fetch_assoc($result1));

    $response = array(
        'message' => 'Order received successfully.'
    );
    flash('Order received successfully.', false);
    return;
}
function getReceivedOrders($con)
{
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='received' OR `order_status`='partially_returned'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do {
        $arr[$i++] = $row;
    } while ($row = mysqli_fetch_assoc(($result)));
    return json_encode($arr);
}
?>