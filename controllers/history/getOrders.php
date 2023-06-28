<?php
function getOrdersData($con, $order_id)
{
    $query = "SELECT * FROM `orderdata` WHERE `order_id`='$order_id'";
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