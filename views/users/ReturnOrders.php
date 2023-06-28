<?php
include('../layouts/boilerplate.php');
include('../../controllers/users/RetreiveOrderDetails.php');
$arr=[];
$arr=json_decode(getReceivedOrders($con),true);

echo'<h1>Tap On The Order You Need To Return</h1><br>';
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
                <td><a class="btn btn-info" href="../stock/OrderReturnDetails.php?order_id=' .$arr[$i]['order_id']. '">View Details</a>
                </td>
            </tr>
            ';
            }
            
        echo '</tbody>
    </table>';
}else{
    echo 'No Orders Available!';
}
echo '</div>
    </div>
</div>';
include('../layouts/boilerplate_footer.php');
?>
<script>$('#data-table').DataTable();</script>