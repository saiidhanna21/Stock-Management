<?php
$order_id = $_GET["order_id"];
include('../layouts/boilerplate.php');
include('../../controllers/history/getOrders.php');
if (isset($_SESSION['flash'])) {
    echo $_SESSION['flash'];
    unset($_SESSION['flash']);
}
$arr=[];
$arr=json_decode(getOrdersData($con,$order_id),true);
echo'<h1>Order Details For Order Id '.$order_id.'</h1><br>';
if(!empty($arr[0])){
    echo '
    <div class="card">
    <div class="card-body">
<div class="table-responsive">
    <table class="table table-sm text-center mx-auto">
        <thead>
            <tr>
            <th scope="col">Order Line</th>
            <th scope="col">Item Nb</th>
            <th scope="col">Item Name</th>
            <th scope="col">Expiry Date</th>
            <th scope="col">Unit Price</th>
            <th scope="col">Total Amount</th>
            <th scope="col">Primary Unit Price</th>
            <th scope="col">primary Quantity</th>
            <th scope="col">line_status</th>
            </tr>
        </thead>
        <tbody>';
            for($i=0;$i<sizeof($arr);$i++){
                $itemnb=$arr[$i]['item_number'];
                $raw=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM `itemmaster` WHERE `item_number`='$itemnb'"));
                $itemname=$raw['item_name'];
            echo '<tr>
            <th scope="row">'.$arr[$i]['order_line'].'</th>
            <td>'.$arr[$i]['item_number'].'</td>
            <td>'.$itemname.'</td>
            <td>'.$arr[$i]['item_expiry_date'].'</td>
            <td>'.$arr[$i]['unit_price'].'</td>
            <td>'.$arr[$i]['total_unit_amount'].'</td>
            <td>'.$arr[$i]['primary_unit_price'].'</td>
            <td>'.$arr[$i]['primary_quantity'].'</td>
            <td>'.$arr[$i]['order_line_status'].'</td>
            </tr>
            ';
            }
        echo '</tbody>
    </table>
</div>
    </div>
</div>';
}else{
    echo 'No Pending Orders Are Available!';
}echo '<form action="ReturnedOrders.php">
<Button class="btn btn-danger" style="width: 100px; margin-top:5px">Back</Button></form>
</form>';

include('../layouts/boilerplate_footer.php');
?>