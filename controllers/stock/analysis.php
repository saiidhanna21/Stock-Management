<?php
function getExpiredItems($con, $itemNb)
{
    $curDate = date('m');
    $mon1 = date("F", mktime(0, 0, 0, $curDate - 1, 10));
    $mon2 = date("F", mktime(0, 0, 0, $curDate - 2, 10));
    $mon3 = date("F", mktime(0, 0, 0, $curDate - 3, 10));
    $arr = array();

    $result = mysqli_query($con, "SELECT SUM(quantity) as qty, `Month` FROM `expireditems` WHERE `Month`='$mon3' and `item_number`=$itemNb");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['qty']) {
            $arr[$mon3] = $row['qty'];
        } else {
            $arr[$mon3] = 0;
        }
    }

    $result = mysqli_query($con, "SELECT SUM(quantity) as qty, `Month` FROM `expireditems` WHERE `Month`='$mon2' and `item_number`=$itemNb");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['qty']) {
            $arr[$mon2] = $row['qty'];
        } else {
            $arr[$mon2] = 0;
        }
    }
    $result = mysqli_query($con, "SELECT SUM(quantity) as qty, `Month` FROM `expireditems` WHERE `Month`='$mon1' and `item_number`=$itemNb");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['qty']) {
            $arr[$mon1] = $row['qty'];
        } else {
            $arr[$mon1] = 0;
        }
    }

    return json_encode($arr);
}
function getQuantityItems($con, $itemNb)
{
    $arr = array();
    $curDate = date("m");
    $curYear = date("Y");
    $last1Mon = $curDate - 1;
    $last2Mon = $curDate - 2;
    $last3Mon = $curDate - 3;
    $mon1 = date("F", mktime(0, 0, 0, $curDate - 1, 10));
    $mon2 = date("F", mktime(0, 0, 0, $curDate - 2, 10));
    $mon3 = date("F", mktime(0, 0, 0, $curDate - 3, 10));
    $result = mysqli_query($con, "SELECT SUM(orderdata.primary_quantity) as sum FROM `orderdata` JOIN `orderheader` on orderdata.order_id=orderheader.order_id where (orderheader.order_status='received' OR orderheader.order_status='partially_returned') AND (orderdata.item_expiry_date BETWEEN '$curYear-$last3Mon-1' AND '$curYear-$last3Mon-31') and orderdata.item_number=$itemNb AND orderdata.order_line_status='accepted';");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row["sum"]) {
            $arr["$mon3"] = $row["sum"];
        } else {
            $arr[$mon3] = 0;
        }
    }
    $result = mysqli_query($con, "SELECT SUM(orderdata.primary_quantity) as sum FROM `orderdata` JOIN `orderheader` on orderdata.order_id=orderheader.order_id where (orderheader.order_status='received' OR orderheader.order_status='partially_returned') AND (orderdata.item_expiry_date BETWEEN '$curYear-$last2Mon-1' AND '$curYear-$last2Mon-31') and orderdata.item_number=$itemNb AND orderdata.order_line_status='accepted';");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row["sum"]) {
            $arr["$mon2"] = $row["sum"];
        } else {
            $arr[$mon2] = 0;
        }
    }
    $result = mysqli_query($con, "SELECT SUM(orderdata.primary_quantity) as sum FROM `orderdata` JOIN `orderheader` on orderdata.order_id=orderheader.order_id where (orderheader.order_status='received' OR orderheader.order_status='partially_returned') AND (orderdata.item_expiry_date BETWEEN '$curYear-$last1Mon-1' AND '$curYear-$last1Mon-31') and orderdata.item_number=$itemNb AND orderdata.order_line_status='accepted';");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row["sum"]) {
            $arr["$mon1"] = $row["sum"];
        } else {
            $arr[$mon1] = 0;
        }
    }
    return json_encode($arr);
}
function fluctuationPrice($con, $itemNumber)
{
    $data = array();


    for ($i = 3; $i >= 1; $i--) {
        $query = "SELECT AVG(primary_unit_price) , MONTH(order_date)
    FROM orderdata
    JOIN orderheader on orderheader.order_id = orderdata.order_id and item_number = $itemNumber
    WHERE orderheader.order_status='received' AND orderheader.order_id IN (SELECT order_id
    FROM orderheader
    WHERE orderheader.order_status='received' AND MONTH(order_date) = MONTH(DATE_ADD(CURRENT_DATE,INTERVAL -" . $i . " MONTH))
    AND YEAR(order_date) = YEAR(DATE_ADD(CURRENT_DATE,INTERVAL -" . $i . " MONTH)))";


        $answer = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($answer);
        if ($row['AVG(primary_unit_price)'] == NULL) {
            continue;
        } else {
            $monthName = date("F", mktime(0, 0, 0, $row['MONTH(order_date)'], 10));
            $data[$monthName] = $row['AVG(primary_unit_price)'];
        }
    }
    return json_encode(array('prices' => $data));
}
?>