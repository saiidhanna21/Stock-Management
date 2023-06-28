<?php
include('../layouts/boilerplate.php');
?>
<?php
if(!empty($_SESSION["makeOrder"])){
    echo(flash('Your Order Is Ordered succefully',false));
    unset($_SESSION["makeOrder"]);
}
include('../functions.php');
$jsonData = checkStock($con);
$data = json_decode($jsonData, true);
echo '<div class="page-header no-gutters">
    <div class="row align-items-md-center">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-affix m-v-10">
                        <i class="prefix-icon anticon anticon-search opacity-04"></i>
                        <input type="text" class="form-control" id="searchStock" placeholder="Search Stock">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="text-md-right m-v-10">
                <div class="btn-group m-r-10">
                    <button id="card-view-btn" type="button" class="btn btn-default btn-icon active" data-toggle="tooltip" data-placement="bottom" title="Card View">
                        <i class="anticon anticon-appstore"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div id="card-view">
        <div class="row">';
    if ($data != null) {
        $stockData = $data['stock_data'];

    foreach ($stockData as $item) {
        $itemNumber = $item['item_number'];
        $itemName = $item['item_name'];
        $itemDescription = $item['item_description'];
        $itemImage = $item['item_image'];
        $quantity = $item['primary_quantity'];
        $initialQuantity = $item['primary_initial_quantity'];
        echo '
                <div class="col-md-3 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="media">
                                    <div class="avatar avatar-image rounded">
                                        <img src="../../assets/' . $itemImage . '" alt="">
                                    </div>
                                    <div class="m-l-10">
                                        <h5 class="m-b-0">' . $itemName . '</h5>
                                        <span class="text-muted font-size-13">' . $quantity . ' items</span>
                                    </div>
                                </div>
                                <div class="dropdown dropdown-animated scale-left">
                                    <a class="text-gray font-size-18" href="javascript:void(0);" data-toggle="dropdown">
                                        <i class="anticon anticon-ellipsis"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <form method="get" id="myForm" action="show.php">
                                            <input type="hidden" value="' . $itemNumber . '" name="itemNumber"/>
                                            <button class="dropdown-item" onclick="submitForm()">
                                                <i class="anticon anticon-eye"></i>
                                                <span class="m-l-10">View</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <p class="m-t-25">' . $itemDescription . '</p>
                            <div class="m-t-30">
                                <div class="d-flex justify-content-between">
                                    <span class="font-weight-semibold">Items Left</span>
                                    <span class="font-weight-semibold">' . intval(($quantity / $initialQuantity) * 100) . '%</span>
                                </div>
                                <div class="progress progress-sm m-t-10">';
        $percentage = ($quantity / $initialQuantity) * 100;
        $progressClass = 'bg-success';

        if ($percentage < 30) {
            $progressClass = 'bg-danger';
        } elseif ($percentage < 70) {
            $progressClass = 'bg-info';
        }
        echo ' <div class="progress-bar ';
        echo $progressClass;
        echo ' " role="progressbar" style="width: ';
        echo intval($percentage);
        echo '%"></div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';
        }
    } else {
        echo 'No Stocks Available!';
    }?>
        </div>
    </div>
</div>
<script>
    function submitForm() {
        document.getElementById("myForm").submit();
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchStock').on('input', function() {
            var searchText = $(this).val().toLowerCase();

            $('#card-view .card').each(function() {
                var itemName = $(this).find('h5').text().toLowerCase();

                if (itemName.indexOf(searchText) === -1) {
                    $(this).css('visibility', 'hidden');
                } else {
                    $(this).css('visibility', 'visible');
                }
            });
        });
    });
</script>
<?php
include('../layouts/boilerplate_footer.php');
?>