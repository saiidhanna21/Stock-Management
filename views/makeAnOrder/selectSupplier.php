<?php
session_start();
if (empty($_SESSION["loggedIn"])) {
    include('../partials/flash.php');
    $_SESSION["notLoggedIn"] = "true";
    $_SESSION["flash"] = flash("Unauthorized Access!! You must be logged in", true);
    header("location:http://localhost/fyp_project/views/users/login.php");
    exit();
}
include('../layouts/boilerplate2.php');
include_once('../../utils/connect.php');
include('../../controllers/orders/supplier.php');

// hone lezem a3mel shi flash lemen byerja3 men selectItems bala ma ykun mna22a supplier

?>
<h1>Make An Order</h1>
<div class="card">
    <div class="card-body">
<h3>Select A Supplier</h3>

<form action="selectItems.php" method="post">
<div class="m-b-15">
    <select class="select2" name="supplier_id">
        <?php
            $json = json_decode(getSupplier($con),true);
            $supplierData = $json['supplier_data'];
            if(empty($_SESSION['supplier_id'])){
            for($i=0; $i<sizeof($supplierData);$i++){
                echo '<option value="'.$supplierData[$i]['supplier_id'].'">'.$supplierData[$i]['supplier_id'].'</option>';
                
            }
        }else{
            $supplierId = $_SESSION['supplier_id'];
            for($i=0; $i<sizeof($supplierData);$i++){
                if($supplierId == $supplierData[$i]['supplier_id']){
                    echo '<option value="'.$supplierData[$i]['supplier_id'].'" selected>'.$supplierData[$i]['supplier_id'].'</option>';
                }else{
                    echo '<option value="'.$supplierData[$i]['supplier_id'].'">'.$supplierData[$i]['supplier_id'].'</option>';
                }
            }
        }
            
            
        ?>
        
    </select>
</div>


        
 
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Location</th>
            </tr>
        </thead>
        <tbody>
            <?php
            for($j=0 ; $j<sizeof($supplierData);$j++){
                echo'<tr>
                <th scope="row">'.$supplierData[$j]['supplier_id'].'</th>
                <td>'.$supplierData[$j]['supplier_name'].'</td>
                <td>'.$supplierData[$j]['supplier_location'].'</td>
                
                </tr>';
            }
            ?>
        </tbody>
    </table>
</div>


<button class="btn btn-primary btn-tone m-r-5">Next</button>
</form>
</div>
</div>
<?php
include('../layouts/boilerplate_footer.php');
?>