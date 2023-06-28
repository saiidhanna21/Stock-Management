<?php
function makeAnOrder($con ,$supplierId , $itemsNb , $itemsQty){
    //control input
    $control =controlInput($con , $supplierId , $itemsNb , $itemsQty);
    if($control !=0){
        return json_encode(array('quantity_error'=>$control));
    }
    

    //insert entry into order headers : order id , purchase type , suppier id , current date , order status(pending by default)
    $orderId = addOrderHeader($con , $supplierId);

    //initialize order lines to 1
    $orderLine = 1;
    
    //insert order data
    //in a for loop for the size of items ordered
    for ($i=0; $i<sizeof($itemsNb);$i++){

        //save item ordered
        $itemNumber = $itemsNb[$i];

        //save quantity ordered
        $orderedQuantity = $itemsQty[$i];

        // get the item's needed details (uoms and pricing )
        $query = "SELECT uom_purchasing , uom_pricing , uom_selling , pricing_amount 
        FROM itemmaster
        INNER JOIN supplieritems ON itemmaster.item_number = supplieritems.item_number
        WHERE itemmaster.item_number =$itemNumber 
            AND supplieritems.supplier_id =$supplierId ";
        
        $result = mysqli_query($con , $query);
        $details = mysqli_fetch_assoc($result);

        $uomPurchasing = $details['uom_purchasing'];
        $uomPricing = $details['uom_pricing'];
        $uomSelling = $details['uom_selling'];
        $pricingAmount = $details['pricing_amount'];

        //fill unit_uom , calculate unit_price , primary_unit_price
        $unitUom = $uomPurchasing;
        
        $unitPrice = getUnitPrice($con , $uomPricing , $uomPurchasing , $pricingAmount , $itemNumber);
        $primaryUnitPrice = getUnitPrice($con , $uomPricing , $uomSelling , $pricingAmount, $itemNumber);



        //check quantities and expiry dates
        $query = "SELECT item_expiry_date , produced_quantity
                  FROM supplieritems
                  WHERE supplier_id = $supplierId AND item_number = $itemNumber
                  ORDER BY item_expiry_date 
                  ";
        $result = mysqli_query($con , $query);




        //initialize quantity , expiry dates array , rest quantities
        $qty = $orderedQuantity;
        $expiryDates = [];
        $restQuantities = [];
        $quantityOut = [];
        while($qty !=0){
            $row = mysqli_fetch_assoc($result);
            $expiryDate = $row['item_expiry_date'];
            $producedQuantity = $row['produced_quantity'];
            
            if($producedQuantity==0){
                continue;
            }elseif($producedQuantity>=$qty){
                $out = $qty;
                
                $producedQuantity = $producedQuantity - $qty;
                $qty =0;
                
            }
            else{
                $out = $producedQuantity;
                $qty = $qty - $producedQuantity;
                $producedQuantity = 0;
                
            }
            array_push($restQuantities , $producedQuantity);
            array_push($quantityOut , $out);
            array_push($expiryDates ,$expiryDate);
        
            
        }

        
        // update supplier items , do calculations for order data and insert in orderdata table
        for($j =0 ; $j<sizeof($expiryDates) ; $j++){
            $expiryDate = $expiryDates[$j];
            $producedQuantity = $restQuantities[$j];


            //update produced quantity in supplierItems
            $query = "UPDATE supplieritems
                      SET produced_quantity = '$producedQuantity'
                      WHERE item_expiry_date = '$expiryDate' and item_number='$itemNumber' and supplier_id ='$supplierId'";
            mysqli_query($con, $query);


            //calculate total_unit_amount
            $unitQuantity = $quantityOut[$j];
            $totalUnitAmount = getTotalUnitAmount($unitQuantity , $unitPrice);

            //calculate primary quantity
            $primaryQuantity=getPrimaryQuantity($con , $unitQuantity , $uomPurchasing , $uomSelling, $itemNumber);


            //insert order data row
            $query = "INSERT INTO orderdata(order_id , order_line , item_number , unit_quantity , unit_uom , unit_price , total_unit_amount , primary_unit_price , primary_quantity , item_expiry_date)
                    VALUES ($orderId , $orderLine , $itemNumber , $unitQuantity , '$unitUom' ,$unitPrice ,$totalUnitAmount,$primaryUnitPrice,$primaryQuantity,'$expiryDate')";

            mysqli_query($con , $query);




            //new order line
            $orderLine = $orderLine +1;


        }
        
}
return json_encode(array('quantity_error'=>0)); 
}


function controlInput($con , $supplierId , $itemsNb , $itemsQty){
    for($w=0;$w<sizeof($itemsNb);$w++){
        $query ="SELECT SUM(produced_quantity)
                 FROM supplieritems
                 WHERE item_number= $itemsNb[$w] AND supplier_id = $supplierId";

        $result =mysqli_query($con , $query);
        $row = mysqli_fetch_assoc($result);
        if($row['SUM(produced_quantity)']<$itemsQty[$w]){
            return $itemsNb[$w];
        }
    }
    return 0;

}


function addOrderHeader($con , $supplierId){
    
    $query = "INSERT INTO orderheader (supplier_id , order_date )
              VALUES ($supplierId , CURRENT_DATE)";
    $result = mysqli_query($con , $query);
    
    
    $query = "SELECT LAST_INSERT_ID()
              FROM orderheader";
    $result = mysqli_query($con , $query);
    $row = mysqli_fetch_assoc($result);
    return $row['LAST_INSERT_ID()'];

}

function getUnitPrice($con ,$uomPricing, $uom, $pricingAmount , $itemNumber){
    $value = $pricingAmount;
    //NOTE uom is purchasing or Selling
    if($uomPricing== $uom){
        return $value;
    }
    while($uomPricing != $uom){
        $query ="SELECT conversion_factor , to_uom
                 FROM uomconversion
                 WHERE from_uom = '$uomPricing' AND item_number = $itemNumber";
        $result = mysqli_query($con , $query);
        if($row = mysqli_fetch_assoc($result)){
            $uomPricing = $row['to_uom'];
            $value = $value / $row['conversion_factor'];
        }else{
            $query ="SELECT conversion_factor , from_uom
                 FROM uomconversion
                 WHERE to_uom = '$uomPricing' AND item_number = $itemNumber";
            $result = mysqli_query($con , $query);
            $row = mysqli_fetch_assoc($result);
            $uomPricing = $row['from_uom'];
            $value = $row['conversion_factor'] * $value;

        }

    }
    return round($value,2);

}

function getTotalUnitAmount($unitQuantity , $unitPrice){
    return $unitPrice * $unitQuantity;
}

function getPrimaryQuantity($con , $unitQuantity , $uomPurchasing , $uomSelling , $itemNumber){
    $value = $unitQuantity;
    if($uomPurchasing == $uomSelling){
        return $value;
    }
    while($uomPurchasing != $uomSelling){
        $query ="SELECT conversion_factor , to_uom
                 FROM uomconversion
                 WHERE from_uom = '$uomPurchasing' AND item_number = $itemNumber";
        $result = mysqli_query($con , $query);
        //here else not needed since usually the selling uom is never bigger than the purchasing uom
        // if there is such a case then code need to be modified
        $row = mysqli_fetch_assoc($result);
        $uomPurchasing = $row['to_uom'];
        $value = $value * $row['conversion_factor'];
        }
        return $value;
    }
?>