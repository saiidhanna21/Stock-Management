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
?>

