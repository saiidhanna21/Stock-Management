<?php
function checkExpiredItems($con)
{
    $result = mysqli_query($con, "SELECT item_number,item_expiry_date,primary_quantity_batch FROM `stockdetails` WHERE item_expiry_date<CURRENT_DATE");
    while ($row = mysqli_fetch_assoc($result)) {
        $item_number = $row['item_number'];
        $item_expiry_date = $row['item_expiry_date'];
        $qty = $row['primary_quantity_batch'];
        $month = date('F', strtotime($item_expiry_date));
        mysqli_query($con, "INSERT INTO `expireditems`(`Month`, `item_number`, `quantity`, `item_expiry_date`) VALUES ('$month','$item_number','$qty','$item_expiry_date');");
        $row = mysqli_fetch_assoc(mysqli_query($con, "SELECT primary_quantity FROM `stock` where `item_number`=$item_number"));
        $qtyStock = $row['primary_quantity'];
        $qtyStock = $qtyStock - $qty;
        if ($qtyStock > 0) {
            mysqli_query($con, "UPDATE `stock` set `primary_quantity`='$qtyStock' where `item_number`='$item_number';");
        } else {
            mysqli_query($con, "DELETE FROM `stock` where `item_number`='$item_number';");
        }
    }
    mysqli_query($con, "DELETE FROM `stockdetails` WHERE item_expiry_date<CURRENT_DATE");
}
?>