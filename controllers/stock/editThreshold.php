<?php
function editThreshold($con, $itemNumber, $threshold)
{
    include_once('../partials/flash.php');
    session_start();
    $sql = "UPDATE `stock` SET `threshold`='$threshold' WHERE item_number='$itemNumber'";
    mysqli_query($con, $sql);
    $_SESSION["flash"] = flash("Treshold Updated Successfully!", false);
}
?>