<?php
function returnOrder($con, $id, $lineId)
{
    $arr = [];
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `supplier_id` from `orderheader` where `order_id`='$id';")));
    $arr['supId'] = $row['supplier_id'];
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT * from `orderdata` where `order_id`='$id' and `order_line`='$lineId';")));
    $arr['expiry'] = $row['item_expiry_date'];
    $arr['quantity'] = $row['primary_quantity'];
    $arr['item_number'] = $row['item_number'];
    $arr['unit_quantity'] = $row['unit_quantity'];
    $expiry = $arr['expiry'];
    $quantity = $arr['quantity'];
    $item_number = $arr['item_number'];
    $uquantity = $arr['unit_quantity'];
    $supId = $arr['supId'];
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `produced_quantity` from `supplieritems` where `supplier_id`='$supId' and `item_number`='$item_number' and `item_expiry_date`='$expiry';")));
    $supQuantity = $row['produced_quantity'];
    $total = $uquantity + $supQuantity;
    if ($row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `primary_quantity` from `stock` where `item_number`='$item_number';")))) {
        $stockQuantity = $row['primary_quantity'];
        $newStockQuantity = $stockQuantity - $quantity;
        if ($newStockQuantity >= 0) {
            if ($row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `primary_quantity_batch` from `stockdetails` where `item_number`='$item_number' and `item_expiry_date`='$expiry';")))) {
                $stockDetailsQuantity = $row['primary_quantity_batch'];
                $newStockDQuantity = $stockDetailsQuantity - $quantity;
                if ($newStockDQuantity >= 0) {
                    if ($newStockQuantity > 0) {
                        mysqli_query($con, "UPDATE `stock` set `primary_quantity`='$newStockQuantity' where `item_number`='$item_number';");
                    } elseif ($newStockQuantity == 0) {
                        mysqli_query($con, "DELETE FROM `stock` where `item_number`='$item_number';");
                    }
                    mysqli_query($con, "UPDATE `supplieritems` set `produced_quantity`='$total' where `supplier_id`='$supId' and `item_number`='$item_number' and `item_expiry_date`='$expiry';");
                    mysqli_query($con, "UPDATE `orderdata` set `order_line_status`='returned' where `order_id`='$id' and `order_line`='$lineId';");
                    if (mysqli_fetch_assoc((mysqli_query($con, "SELECT * from `orderdata` where `order_id`='$id' and `order_line_status`='accepted';")))) {
                        mysqli_query($con, "UPDATE `orderheader` set `order_status`='partially_returned' where `order_id`='$id';");
                    } else {
                        mysqli_query($con, "UPDATE `orderheader` set `order_status`='returned' where `order_id`='$id';");
                    }
                }
                if ($newStockDQuantity > 0) {
                    mysqli_query($con, "UPDATE `stockdetails` set `primary_quantity_batch`='$newStockDQuantity' where `item_number`='$item_number' and `item_expiry_date`='$expiry';");
                } elseif ($newStockDQuantity == 0) {
                    mysqli_query($con, "DELETE FROM `stockdetails` where `item_number`='$item_number' and `item_expiry_date`='$expiry';");
                } else {
                    echo (flash('<span style="color:red">WARNING:</span> You Do Not Have The Enough Quantity Left In Stock Details To Return Order Line <span style="color:red">' . $lineId . '</span> To His Supplier<br>', true));
                }
            } else {
                echo (flash('<span style="color:red">WARNING:</span> You Do Not Have The Item In Order Line <span style="color:red">' . $lineId . '</span> In Your Stock Details To Return It To His Supplier<br>', true));
            }
        } else {
            echo (flash('<span style="color:red">WARNING:</span> You Do Not Have The Enough Quantity Left In Stock To Return Order Line <span style="color:red">' . $lineId . '</span> To His Supplier<br>', true));
        }
    } else {
        echo (flash('<span style="color:red">WARNING:</span> You Do Not Have The Item In Order Line <span style="color:red">' . $lineId . '</span> In Your Stock To Return It To His Supplier<br>', true));
    }

    return json_encode(array('supplierItems_data' => $arr));
}
function getReturnedOrders($con)
{
    $query = "SELECT * FROM `orderheader` WHERE `order_status`='returned' OR `order_status`='partially_returned'";
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