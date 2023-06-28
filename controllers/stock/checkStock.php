<?php
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
?>