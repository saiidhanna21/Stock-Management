<?php
include('../layouts/boilerplate.php');
include('../../controllers/history/returnOrders.php');
include('../../controllers/history/receiveOrders.php');
$orders=array();
if(isset($_POST["orders"])){
    $orders=$_POST["orders"];
    for($i=0;$i<sizeof($orders);$i++){
        receiveOrder($con,$orders[$i]);
    }}
    $arr=[];
$arr=json_decode(getReturnedOrders($con),true);
echo'<h1>Returned Orders History</h1><br>';
if(!empty($arr[0])){
    echo '
    <div class="card">
    <div class="card-body">
<div class="table-responsive">
    <table class="table table-sm text-center mx-auto" id="data-table">
        <thead>
            <tr>
                <th scope="col">Order_id</th>
                <th scope="col">Supplier_id</th>
                <th scope="col">Order_Date</th>
                <th scope="col">Order_Status</th>
                <th scope="col">View Details</th>
            </tr>
        </thead>
        <tbody>';
        for($i=0;$i<sizeof($arr);$i++){
            echo '<tr>
                <th scope="row">'.$arr[$i]['order_id'].'</th>
                <td>'.$arr[$i]['supplier_id'].'</td>
                <td>'.$arr[$i]['order_date'].'</td>
                <td>'.$arr[$i]['order_status'].'</td>
                <td><a class="btn btn-info" href="ReturnedOrdersDetails.php?order_id=' .$arr[$i]['order_id']. '">View Details</a></td>';
        }
        echo '</tbody>
    </table>
</div>';
}else{
    echo 'No Returned Orders Are Available!';
}
echo '</div>
    </div>
</div>';
include('../layouts/boilerplate_footer.php');
?>
<script>$('#data-table').DataTable();</script>