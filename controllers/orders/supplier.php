<?php
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
?>