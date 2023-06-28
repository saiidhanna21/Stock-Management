<?php
function getStockItem($con, $itemNumber, $amount_needed)
{
    include_once('../partials/flash.php');
    include_once('../email.php');
    session_start();
    $sql = "SELECT * FROM `stock` WHERE `item_number`=$itemNumber";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $threshold = $row["threshold"];
        $flag = $row["flag"];
        if ($row['primary_quantity'] < $amount_needed) {
            $_SESSION["flash"] = flash("Not enough Items.", true);
            return;
        } else {
            $qty_stock_left = $row['primary_quantity'] - $amount_needed;
            $sql = "SELECT * FROM `stockdetails` 
            WHERE `item_number`=$itemNumber 
            ORDER BY `item_expiry_date` ASC";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            do {
                $item_number = $row['item_number'];
                $item_expiry_date = $row['item_expiry_date'];
                $qty_batch = $row['primary_quantity_batch'];
                if ($qty_batch >= $amount_needed) {
                    $qty_left = $qty_batch - $amount_needed;
                    $sql = "UPDATE `stockdetails` SET `primary_quantity_batch`='$qty_left' WHERE `item_number`='$item_number' AND `item_expiry_date`='$item_expiry_date';";
                    mysqli_query($con, $sql);
                    $sql = "UPDATE `stock` SET `primary_quantity`='$qty_stock_left' WHERE `item_number`='$itemNumber'";
                    mysqli_query($con, $sql);
                    if ($qty_left < $threshold && $flag == 1) {
                        sendEmail($con, $item_number);
                        $_SESSION["flash"] = flash("Item succesfully taken! And the admin has been notified!", false);
                        return;
                    }
                    $_SESSION["flash"] = flash("Succesfully taken!", false);
                    return;
                } else {
                    $amount_needed -= $qty_batch;
                    $sql = "UPDATE `stockdetails` SET `primary_quantity_batch`='0' WHERE `item_number`='$item_number' AND `item_expiry_date`='$item_expiry_date';";
                    mysqli_query($con, $sql);
                }
            } while ($row = mysqli_fetch_assoc($result));
        }
    }
}
?>