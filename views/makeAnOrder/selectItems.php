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
if(isset($_SESSION['items'])){
    include('../layouts/boilerplate2.php');
    if(empty($_POST['supplier_id'])){
        $new = false;
    }else{
        
        if($_SESSION['supplier_id'] == $_POST['supplier_id']){
            $new = false;
        }else{
            $_SESSION['supplier_id'] = $_POST['supplier_id'];
            $new = true;
        }
    }
}
elseif(empty($_POST['supplier_id'])){
    // i can use this for flash $_SESSION['error'] = true;
    if(empty($_POST['items'])){
        if(isset($_SESSION['supplier_id'])){

        
        include('../layouts/boilerplate2.php');
        $new = true;
        }else{
            header('Location:selectSupplier.php');
        }
    }else{header('Location:selectSupplier.php');}
    
    
    
}elseif(!empty($_POST['supplier_id'])){
    include('../layouts/boilerplate2.php');
    

        $new = true;
        $_SESSION['supplier_id'] = $_POST['supplier_id'];
    }

include('../../controllers/orders/supplier.php');
?>
<h1>Make An Order</h1>
<h4>Select Items</h4>
<form action="selectQuantity.php" method="post">
<div class="card">
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-hover e-commerce-table" id="data-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Purchase UOM</th>
                        <th>Pricing UOM</th>
                        <th>Pricing</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $json = json_decode(getSupplierItems($con,$_SESSION['supplier_id']),true);
                        $supplierItems = $json['supplier_items'];
                        if($new==false){
                            $items = $_SESSION['items'];
                        
                            for($i=0; $i<sizeof($supplierItems);$i++){
                            if(in_array($supplierItems[$i]['item_number'],$items)){
                                
                                echo'<tr>
                            <td>
                                <div class="checkbox">
                                    <input id="check-item-'.$supplierItems[$i]['item_number'].'" type="checkbox" name ="items[]" value="'.$supplierItems[$i]['item_number']
                                    .'"checked>
                                    <label for="check-item-'.$supplierItems[$i]['item_number'].'" class="m-b-0"></label>
                                </div>
                            </td>
                            <td>#'.$supplierItems[$i]['item_number'].'</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-image avatar-sm m-r-10">
                                        <img src="../../assets/'.$supplierItems[$i]['item_image'].'" alt="">
                                        </div>
                                        <h6 class="m-b-0">'.$supplierItems[$i]['item_name'].'</h6>
                                        </div>
                                    </td>
                                    <td>'.$supplierItems[$i]['uom_purchasing'].'</td>
                                    <td>'.$supplierItems[$i]['uom_pricing'].'</td>
                                    <td>'.$supplierItems[$i]['pricing_amount'].'$</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            
                                            <div>'.$supplierItems[$i]['item_description'].'</div>
                                            </div>
                                        </td>
                                       
                                    </tr>
                ';
                            }else{
                                echo'<tr>
                            <td>
                                <div class="checkbox">
                                    <input id="check-item-'.$supplierItems[$i]['item_number'].'" type="checkbox" name ="items[]" value="'.$supplierItems[$i]['item_number']
                                    .'">
                                    <label for="check-item-'.$supplierItems[$i]['item_number'].'" class="m-b-0"></label>
                                </div>
                            </td>
                            <td>#'.$supplierItems[$i]['item_number'].'</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-image avatar-sm m-r-10">
                                        <img src="../../assets/'.$supplierItems[$i]['item_image'].'" alt="">
                                        </div>
                                        <h6 class="m-b-0">'.$supplierItems[$i]['item_name'].'</h6>
                                        </div>
                                    </td>
                                    <td>'.$supplierItems[$i]['uom_purchasing'].'</td>
                                    <td>'.$supplierItems[$i]['uom_pricing'].'</td>
                                    <td>'.$supplierItems[$i]['pricing_amount'].'$</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            
                                            <div>'.$supplierItems[$i]['item_description'].'</div>
                                            </div>
                                        </td>
                                       
                                    </tr>
                ';
                            }
                        }
                        
                        
                    }elseif($new){
                        for($i=0; $i<sizeof($supplierItems);$i++){
                            
                            echo'<tr>
                            <td>
                                <div class="checkbox">
                                    <input id="check-item-'.$supplierItems[$i]['item_number'].'" type="checkbox" name ="items[]" value="'.$supplierItems[$i]['item_number']
                                    .'">
                                    <label for="check-item-'.$supplierItems[$i]['item_number'].'" class="m-b-0"></label>
                                </div>
                            </td>
                            <td>#'.$supplierItems[$i]['item_number'].'</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-image avatar-sm m-r-10">
                                        <img src="../../assets/'.$supplierItems[$i]['item_image'].'" alt="">
                                        </div>
                                        <h6 class="m-b-0">'.$supplierItems[$i]['item_name'].'</h6>
                                        </div>
                                    </td>
                                    <td>'.$supplierItems[$i]['uom_purchasing'].'</td>
                                    <td>'.$supplierItems[$i]['uom_pricing'].'</td>
                                    <td>'.$supplierItems[$i]['pricing_amount'].'$</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            
                                            <div>'.$supplierItems[$i]['item_description'].'</div>
                                            </div>
                                        </td>
                                       
                                    </tr>
                ';
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    
                    



<button class="btn btn-primary btn-tone m-r-5" type="submit">Next</button>
</form>
</div>
</div>
<button class="btn btn-primary btn-tone m-r-5"onclick="window.location='selectSupplier.php'" >Previous</button>
<?php
include('../layouts/boilerplate_footer.php');
?>
<script>
    $('#data-table').DataTable();
    </script>
    