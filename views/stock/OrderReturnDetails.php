<?php
include('../layouts/boilerplate.php');
include('../../controllers/users/RetreiveOrderDetails.php');
if (isset($_SESSION['flash'])) {
    echo $_SESSION['flash'];
    unset($_SESSION['flash']);
}
if(isset($_GET["order_id"])){
    $order_id = $_GET["order_id"];
}
$orders=array();
if(isset($_POST["order_id"])){
    $order_id = $_POST["order_id"];
    if(isset($_POST["orders"])){
    $orders=$_POST["orders"];
    for($i=0;$i<sizeof($orders);$i++){
        returnOrder($con,$order_id,$orders[$i]);
    }
}}
$arr=array();
$arr=json_decode(getOrdersData($con,$order_id),true);

echo'<h1>Return An Order Line</h1><br>';
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
                <th scope="col">Return Line</th>
            </tr>
        </thead>
        <tbody>';
        
            echo'<form action="'.$_SERVER["PHP_SELF"].'"method="post">';
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
                <td>'.$arr[$i]['order_line_status'].'</td>';
                if($arr[$i]['order_line_status']=="accepted"){
                echo '<td><input type="checkbox" name="orders[]" value="'.$arr[$i]['order_line'].'" id="order" />
                <label for="order">Tap To Return</label></td>';
            }else{
                echo '<td>Already Returned</td>';
            }
            echo '</tr>';
            }
        echo '</tbody>
    </table>
</div>
<input type="hidden" name="order_id" value="'.$order_id.'">
<Button class="btn btn-success" style="width: 100px; margin-top:5px">Submit</Button></form>';
        }else{
            echo 'All The Order Lines Are Returned';
        }
        
echo '</div>
    </div>
    <br><form action="../users/ReturnOrders.php">
        <Button class="btn btn-danger" style="width: 100px; margin-top:5px">Back</Button></form>
</form>
</div>';
include('../layouts/boilerplate_footer.php');
?>
