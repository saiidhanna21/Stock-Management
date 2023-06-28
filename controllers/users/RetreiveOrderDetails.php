<?php include('../../utils/connect.php');
function getPendingOrders($con){
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='pending'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do{
        $arr[$i++] = $row;
    }while($row = mysqli_fetch_assoc(($result)));
    return json_encode($arr);
}
function getReturnedOrders($con){
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='returned' OR `order_status`='partially_returned'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do{
        $arr[$i++] = $row;
    }while($row = mysqli_fetch_assoc(($result)));
    return json_encode($arr);
}
function getReceivedOrders($con){
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='received' OR `order_status`='partially_returned'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do{
        $arr[$i++] = $row;
    }while($row = mysqli_fetch_assoc(($result)));
    return json_encode($arr);
}
function getOrdersData($con,$order_id){
    $query = "SELECT * FROM `orderdata` WHERE `order_id`='$order_id'";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do{
        $arr[$i++] = $row;
    }while($row = mysqli_fetch_assoc(($result)));
    return json_encode($arr);
}
function returnOrder($con,$id,$lineId){
    $arr=[];
    $row=mysqli_fetch_assoc((mysqli_query($con,"SELECT `supplier_id` from `orderheader` where `order_id`='$id';")));
    $arr['supId']=$row['supplier_id'];
    $row = mysqli_fetch_assoc((mysqli_query($con,"SELECT * from `orderdata` where `order_id`='$id' and `order_line`='$lineId';")));
    $arr['expiry']=$row['item_expiry_date'];
    $arr['quantity']=$row['primary_quantity'];
    $arr['item_number']=$row['item_number'];
    $arr['unit_quantity']=$row['unit_quantity'];
    $expiry=$arr['expiry'];
    $quantity=$arr['quantity'];
    $item_number=$arr['item_number'];
    $uquantity=$arr['unit_quantity'];
    $supId=$arr['supId'];
    $row=mysqli_fetch_assoc((mysqli_query($con,"SELECT `produced_quantity` from `supplieritems` where `supplier_id`='$supId' and `item_number`='$item_number' and `item_expiry_date`='$expiry';")));
    $supQuantity=$row['produced_quantity'];
    $total=$uquantity+$supQuantity;
    if($row=mysqli_fetch_assoc((mysqli_query($con,"SELECT `primary_quantity` from `stock` where `item_number`='$item_number';")))){
        $stockQuantity=$row['primary_quantity'];
        $newStockQuantity=$stockQuantity-$quantity;
        if($newStockQuantity>=0){
    if($row=mysqli_fetch_assoc((mysqli_query($con,"SELECT `primary_quantity_batch` from `stockdetails` where `item_number`='$item_number' and `item_expiry_date`='$expiry';")))){
        $stockDetailsQuantity=$row['primary_quantity_batch'];
        $newStockDQuantity=$stockDetailsQuantity-$quantity;
        if($newStockDQuantity>=0){
            if($newStockQuantity>0){
                mysqli_query($con,"UPDATE `stock` set `primary_quantity`='$newStockQuantity' where `item_number`='$item_number';");
            }elseif($newStockQuantity==0){
                mysqli_query($con,"DELETE FROM `stock` where `item_number`='$item_number';");
            }
            mysqli_query($con,"UPDATE `supplieritems` set `produced_quantity`='$total' where `supplier_id`='$supId' and `item_number`='$item_number' and `item_expiry_date`='$expiry';");
            mysqli_query($con,"UPDATE `orderdata` set `order_line_status`='returned' where `order_id`='$id' and `order_line`='$lineId';");
            if(mysqli_fetch_assoc((mysqli_query($con,"SELECT * from `orderdata` where `order_id`='$id' and `order_line_status`='accepted';")))){
                mysqli_query($con,"UPDATE `orderheader` set `order_status`='partially_returned' where `order_id`='$id';");
            }else{
               mysqli_query($con,"UPDATE `orderheader` set `order_status`='returned' where `order_id`='$id';");
           }
        }
    if($newStockDQuantity>0){
        mysqli_query($con,"UPDATE `stockdetails` set `primary_quantity_batch`='$newStockDQuantity' where `item_number`='$item_number' and `item_expiry_date`='$expiry';");
    }elseif($newStockDQuantity==0){
        mysqli_query($con,"DELETE FROM `stockdetails` where `item_number`='$item_number' and `item_expiry_date`='$expiry';");
    }else{
        echo(flash('<span style="color:red">WARNING:</span> You Do Not Have The Enough Quantity Left In Stock Details To Return Order Line <span style="color:red">'.$lineId.'</span> To His Supplier<br>',true));
    }
}else{
    echo( flash('<span style="color:red">WARNING:</span> You Do Not Have The Item In Order Line <span style="color:red">'.$lineId.'</span> In Your Stock Details To Return It To His Supplier<br>',true));
     
}
}else{
    echo(flash('<span style="color:red">WARNING:</span> You Do Not Have The Enough Quantity Left In Stock To Return Order Line <span style="color:red">'.$lineId.'</span> To His Supplier<br>',true));
    
}
}else{
    echo( flash('<span style="color:red">WARNING:</span> You Do Not Have The Item In Order Line <span style="color:red">'.$lineId.'</span> In Your Stock To Return It To His Supplier<br>',true));
    }
    
    return json_encode(array('supplierItems_data'=>$arr));
}
function receiveOrder($con,$orderID){
    //Check if order already received
    $sql = "SELECT `order_status` FROM `orderheader` WHERE `order_id`=$orderID;";
    $result = mysqli_query($con,$sql);
    if(mysqli_num_rows($result)>0){
        $row = mysqli_fetch_assoc($result);
        if($row['order_status']=='received'){
            flash('Order already received',true);
            return;
        }
    }else{
        flash('Order Not Found',true);
        return;
    }
    $sql = "UPDATE `orderheader` SET `order_status` = 'received' WHERE `order_id` = $orderID";
    mysqli_query($con, $sql);    
    $sql = "SELECT * FROM `orderdata` WHERE `order_id` = $orderID";
    $result1 = mysqli_query($con,$sql);
    $row1 = mysqli_fetch_assoc($result1);
    do{
        $itemNumber = $row1['item_number'];
        $primaryQuantity = $row1['primary_quantity'];
        $expiry_date = $row1['item_expiry_date'];
        $primary_unit_price = $row1['primary_unit_price'];
        $sql = "SELECT `primary_quantity` FROM `stock` WHERE `item_number`= $itemNumber";
        $result2 = mysqli_query($con,$sql);
        //Update qty if stock already exists
        if(mysqli_num_rows($result2)>0){
            $row2 = mysqli_fetch_assoc($result2);
            $qty = $row2['primary_quantity'] + $primaryQuantity;
            $sql = "UPDATE `stock` SET `primary_quantity`=$qty,`primary_initial_quantity`=$qty, `flag`=1 WHERE `item_number`=$itemNumber";
            mysqli_query($con,$sql);
        }else{
            //INSERT into stock if stock doesn't exists
            //Treshold default 10
            $sql = "INSERT INTO `stock`(`item_number`, `primary_quantity`, `threshold`, `flag`,`primary_initial_quantity`) VALUES ($itemNumber,$primaryQuantity,10,1,$primaryQuantity)";
            mysqli_query($con,$sql);
        }
        
        //Insert into stock details
        $sql = "SELECT `primary_quantity_batch` FROM `stockdetails` WHERE `item_number`=$itemNumber AND `item_expiry_date`='$expiry_date'";
        $result3 = mysqli_query($con,$sql);

        //Update stock details if same expiry date
        if(mysqli_num_rows($result3)>0){
            $row = mysqli_fetch_assoc($result3);
            $qty = $row['primary_quantity_batch'] + $primaryQuantity;
            $sql = "UPDATE `stockdetails` SET `primary_quantity_batch`=$qty WHERE `item_number`=$itemNumber AND `item_expiry_date`='$expiry_date'";
            mysqli_query($con, $sql);
        //Insert into stock details if different expiry date but item already exists
        }else{
            $sql = "SELECT `shelf_num` FROM `stockdetails` WHERE `item_number`=$itemNumber";
            $result4 = mysqli_query($con, $sql);
            if (mysqli_num_rows($result4) > 0){
                $row = mysqli_fetch_assoc($result4);
                $shelf = $row['shelf_num'];
                $qty = $primaryQuantity; 
                $sql = "INSERT INTO `stockdetails`(`item_number`, `item_expiry_date`, `shelf_num`, `primary_quantity_batch`, `primary_unit_price`) 
                VALUES ('$itemNumber','$expiry_date','$shelf','$qty','$primary_unit_price')";
                mysqli_query($con, $sql);
            //Insert into stock details if different expiry date and item doesn't exists
            }else{
                $sql = "SELECT MAX(`shelf_num`) AS max FROM `stockdetails`";
                $result = mysqli_query($con, $sql);
                $shelf = 1;
                if (!is_null($result)) {
                    $row = mysqli_fetch_assoc($result);
                    $shelf = $row['max'] + 1;
                }
                $sql = "INSERT INTO `stockdetails`(`item_number`, `item_expiry_date`, `shelf_num`, `primary_quantity_batch`, `primary_unit_price`) 
                VALUES ('$itemNumber','$expiry_date','$shelf','$primaryQuantity','$primary_unit_price')";
                mysqli_query($con,$sql);
            }
        }
    }while($row1 = mysqli_fetch_assoc($result1));

    $response = array(
        'message' => 'Order received successfully.'
    );
    flash('Order received successfully.',false);
    return;
}
function checkStock($con){
    $sql = "SELECT * FROM `stock` AS `s` 
    JOIN `itemmaster` AS `i` 
    ON s.item_number=i.item_number";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $arr = array();
    do{
        $arr[] = $row;
    }while($row = mysqli_fetch_assoc($result));
    return json_encode(array('stock_data' => $arr)); 
}
function checkStockDetails($con)
{
    $sql = "SELECT * FROM `stock` AS `s` 
    JOIN `stockdetails` AS `sd`
    ON s.item_number=sd.item_number";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $arr = array();
    do {
        $arr[] = $row;
    } while ($row = mysqli_fetch_assoc($result));
    return json_encode(array('stock_details' => $arr));
}
function getCanceledOrders($con)
{
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='cancelled';";
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