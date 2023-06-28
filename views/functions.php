<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
function sendEmail($con, $item_num)
{
    mysqli_query($con, "UPDATE `stock` SET `flag`=0 WHERE `item_number`='$item_num';");
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `item_name` FROM `itemmaster` WHERE `item_number`='$item_num';")));
    $item_name = $row['item_name'];
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `primary_quantity` FROM `stock` WHERE `item_number`='$item_num';")));
    $quantityLeft = $row['primary_quantity'];
    $row = mysqli_fetch_assoc((mysqli_query($con, "SELECT `email` FROM `admin`;")));
    $adminEmail = $row['email'];
    $mail = new PHPMailer(true);
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->CharSet = "utf-8"; // set charset to utf8
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted

    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->Port = 587; // TCP port to connect to
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->isHTML(true); // Set email format to HTML
    $mail->Username = 'charbelkaraki43@gmail.com'; //your gmail
    $mail->Password = 'nsawjxhdqtvebixn'; //your gmail app password

    $mail->setFrom('charbelkaraki43@gmail.com', 'Stock Management System'); //Your application NAME and EMAIL
    $mail->Subject = 'Stock Refill'; //Message subject
    $itemName = 'kitkat'; // item
    $mail->Body = $item_name . ' item needs to be refilled ( ' . $quantityLeft . ' left). Refill as soon as possible, Thank you'; // Message body
    $mail->addAddress($adminEmail); // Target email


    $mail->send();
}

function getSupplier($con)
{
    $query = "SELECT * FROM `supplier`;";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $arr = [];
    $i = 0;
    do {
        $arr[$i++] = $row;
    } while ($row = mysqli_fetch_assoc(($result)));

    return json_encode(array('supplier_data' => $arr));
}
function getSupplierItems($con, $supplierId)
{
    $query = "SELECT DISTINCT(SI.item_number), IM.item_name , IM.uom_purchasing , IM.uom_pricing , SI.pricing_amount , IM.item_image , IM.item_description
    FROM supplieritems AS SI
    JOIN itemmaster AS IM
    ON SI.item_number = IM.item_number
    WHERE SI.supplier_id = $supplierId";

    $result = mysqli_query($con, $query);

    $i = 0;
    $arr = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $arr[$i++] = $row;
    }
    return json_encode(array('supplier_items' => $arr));
}

function checkStock($con)
{
    $sql = "SELECT * FROM `stock` AS `s` 
    JOIN `itemmaster` AS `i` 
    ON s.item_number=i.item_number";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        $arr = array();
        do {
            $arr[] = $row;
        } while ($row = mysqli_fetch_assoc($result));
        return json_encode(array('stock_data' => $arr));
    }
}
function checkStockDetails($con, $itemNumber)
{
    $sql = "SELECT * FROM `stock` AS `s` 
    JOIN `stockdetails` AS `sd`
    ON s.item_number=sd.item_number
    WHERE s.item_number=$itemNumber";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $arr = array();
        do {
            $arr[] = $row;
        } while ($row = mysqli_fetch_assoc($result));
        return json_encode(array('stock_details' => $arr));
    }
    $response = array(
        'error' => 'Stock Not Found'
    );
    return json_encode($response);
}
function getStockItem($con, $itemNumber, $amount_needed)
{
    include_once('../partials/flash.php');
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
                if ($qty_batch > $amount_needed) {
                    $qty_left = $qty_batch - $amount_needed;
                    $sql = "UPDATE `stockdetails` SET `primary_quantity_batch`='$qty_left' WHERE `item_number`='$item_number' AND `item_expiry_date`='$item_expiry_date';";
                    mysqli_query($con, $sql);
                    $sql = "UPDATE `stock` SET `primary_quantity`='$qty_stock_left' WHERE `item_number`='$itemNumber'";
                    echo $sql;
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
function editThreshold($con, $itemNumber, $threshold)
{
    include_once('../partials/flash.php');
    session_start();
    $sql = "UPDATE `stock` SET `threshold`='$threshold' WHERE item_number='$itemNumber'";
    mysqli_query($con, $sql);
    $_SESSION["flash"] = flash("Treshold Updated Successfully!", false);
}

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
