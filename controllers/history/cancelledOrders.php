<?php
function getCancelledOrders($con)
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