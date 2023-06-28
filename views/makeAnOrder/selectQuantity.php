<?php
session_start();
if (empty($_SESSION["loggedIn"])) {
    include('../partials/flash.php');
    $_SESSION["notLoggedIn"] = "true";
    $_SESSION["flash"] = flash("Unauthorized Access!! You must be logged in", true);
    header("location:http://localhost/fyp_project/views/users/login.php");
    exit();
}
include_once('../../utils/connect.php');
include('../../controllers/orders/makeAnOrder.php');
include('../../controllers/orders/supplier.php');
if (empty($_POST['quantity'])) {


    if (empty($_POST['items'])) {
        // i can use this for flash $_SESSION['error'] = true;
        header('Location:selectItems.php');
    }
}
if (!empty($_POST['items'])) {
    include('../layouts/boilerplate2.php');
    $_SESSION['items'] = $_POST['items'];
} elseif (!empty($_POST['quantity'])) {

    $_SESSION['quantity'] = $_POST['quantity'];

    $json = json_decode(makeAnOrder($con, $_SESSION['supplier_id'], $_SESSION['items'], $_SESSION['quantity']), true);
    $itemError = $json['quantity_error'];
    if ($itemError == 0) {
        //$_SESSION["loggedIn"]="true";
        unset($_SESSION['supplier_id']);
        unset($_SESSION['items']);
        unset($_SESSION['quantity']);
        $_SESSION['makeOrder'] = "true";
        header('Location:../stock/index.php');
        exit();
    } else {
        include('../layouts/boilerplate2.php');
        unset($_SESSION['quantity']);
        echo (flash('error in quantity of item ' . $itemError, TRUE));
    }
}

?>
<h1>Make An Order</h1>
<div class="card">
    <div class="card-body">
        <h4>Enter quantities</h4>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Purchase UOM</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $json = json_decode(getSupplierItems($con, $_SESSION['supplier_id']), true);

                        $orderItems = $json['supplier_items'];
                        for ($i = 0; $i < sizeof($orderItems); $i++) {
                            if (in_array($orderItems[$i]['item_number'], $_SESSION['items'])) {
                                echo '<tr>
                        <th scope="row">' . $orderItems[$i]['item_number'] . '</th>
                        <td>' . $orderItems[$i]['item_name'] . '</td>
                        <td>' . $orderItems[$i]['uom_purchasing'] . '</td>
                        <td>
                <label for="formGroupExampleInput"></label>
                <input type="text" class="form-control" id="formGroupExampleInput" name="quantity[]"';
                                if (isset($_SESSION['quantity'])) {
                                    echo 'value="' . $_SESSION['quantity'][$i] . '"';
                                }
                                echo  ' required> </td>

                </tr>';
                            }
                        }
                        ?>

                    </tbody>
                </table>
            </div>
            <button class="btn btn-success m-r-5">Submit</button>
        </form>
    </div>
</div>
<button class="btn btn-primary btn-tone m-r-5" onclick="window.location='selectItems.php' <?php $_SESSION['previous'] = true ?>">Previous</button>

<?php
include('../layouts/boilerplate_footer.php');
?>