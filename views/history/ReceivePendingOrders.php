<?php
include('../layouts/boilerplate.php');
include('../../controllers/history/receiveOrders.php');
include('../../controllers/history/pendingOrders.php');

if (isset($_SESSION['flash'])) {
    echo $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$orders = array();

if (!empty($_POST["cancel"])) {
    $id = $_POST["cancel"];
    if($_POST["cancel"]){
        mysqli_query($con,"UPDATE `orderheader` set `order_status`='cancelled' WHERE `order_id`='$id'");
        mysqli_query($con,"UPDATE `orderdata` SET `order_line_status`='cancelled' WHERE `order_id`='$id'");
        echo flash("Your order has been cancelled!", false);
    }
}

if (isset($_POST["orders"])) {
    $orders = $_POST["orders"];
    for ($i = 0; $i < sizeof($orders); $i++) {
        receiveOrder($con, $orders[$i]);
    }
}

$arr = [];
$arr = json_decode(getPendingOrders($con), true);
echo '<h1>Tap On The Order You Need To Receive</h1><br>';

if (!empty($arr[0])) {
    echo '
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm text-center mx-auto">
                    <thead>
                        <tr>
                            <th scope="col">Order_id</th>
                            <th scope="col">Supplier_id</th>
                            <th scope="col">Order_Date</th>
                            <th scope="col">Order_Status</th>
                            <th scope="col">View Details</th>
                            <th scope="col">Cancel Orders</th>
                            <th scope="col">Accept Orders</th>
                        </tr>
                    </thead>
                    <tbody>';
    echo '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';

    for ($i = 0; $i < sizeof($arr); $i++) {
        echo '<tr>
                <th scope="row">' . $arr[$i]['order_id'] . '</th>
                <td>' . $arr[$i]['supplier_id'] . '</td>
                <td>' . $arr[$i]['order_date'] . '</td>
                <td>' . $arr[$i]['order_status'] . '</td>
                <td><a class="btn btn-info" href="PendingOrderDetails.php?order_id=' . $arr[$i]['order_id'] . '">View Details</a></td>       
                <td>
                    <button type="button" class="btn btn-danger cancel-btn" data-toggle="modal" data-target="#exampleModal" data-orderid="' . $arr[$i]['order_id'] . '">
                        Cancel
                    </button>
                </td>';

        if ($arr[$i]['order_status'] == "pending") {
            echo '<td><input type="checkbox" name="orders[]" value="' . $arr[$i]['order_id'] . '" id="order" />
                    <label for="order">Tap To Accept</label></td>';
        } else {
            echo '<td>Already Accepted</td>';
        }
    }
    echo '
    </tbody>
    </table>
    <button class="btn btn-primary" style="width: 100px; margin-top:5px">Submit</button>
    </form>
    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cancel Order!</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="anticon anticon-close"></i>
                    </button>
                </div>
                <form id="myForm" action="' . $_SERVER["PHP_SELF"] . '" method="post">
                    <div class="modal-body">
                        <p>Are you sure do you want to cancel this order?</p>
                        <input type="hidden" name="cancel" id="orderIdinput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-danger" onclick="submitForm()">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>';
} else {
    echo 'No Pending Orders Are Available!';
}
?>
</div>
</div>
</div>
<script>
    var cancelButtons = document.getElementsByClassName("cancel-btn");
    for (var i = 0; i < cancelButtons.length; i++) {
        cancelButtons[i].addEventListener("click", function(event) {
            const orderIdVal = document.getElementById("orderIdinput");
            console.log(orderIdVal);
            document.getElementById("orderIdinput").value = event.target.dataset.orderid;
        });
    }

    function submitForm() {
        document.getElementById("myForm").submit();
    }
</script>
<?php
include('../layouts/boilerplate_footer.php');
?>