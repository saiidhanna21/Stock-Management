<?php
include('../../utils/connect.php');
include('../../controllers/stock/checkStock.php');
include('../../controllers/stock/getStock.php');
include('../../controllers/stock/editThreshold.php');
include('../../controllers/stock/analysis.php');

if (!empty($_GET['itemNumber'])) {
    $itemNumber = $_GET['itemNumber'];
    $jsonData = checkStockDetails($con, $itemNumber);
    $data = json_decode($jsonData, true);
    if (!empty($data['error'])) {
        header('Location: http://localhost/fyp_project/views/error.php');
        exit();
    }
    $stockDetails = $data['stock_details'];
    $sql = "SELECT item_name,item_image,item_description FROM itemmaster WHERE item_number=$itemNumber";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
} else {
    header('Location: http://localhost/fyp_project/views/error.php');
    exit();
};
if (!empty($_GET['qty'])) {
    $qty = $_GET['qty'];
    getStockItem($con, $itemNumber, $qty);
    header('Location: http://localhost/fyp_project/views/stock/show.php?itemNumber=' . $itemNumber . '');
    exit();
}
if (!empty($_GET['threshold'])) {
    $threshold = $_GET['threshold'];
    editThreshold($con, $itemNumber, $threshold);
    header('Location: http://localhost/fyp_project/views/stock/show.php?itemNumber=' . $itemNumber . '');
    exit();
}
include('../layouts/boilerplate.php');
if (isset($_SESSION['flash'])) {
    echo $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<div class="page-header no-gutters has-tab">
    <div class="d-md-flex m-b-15 align-items-center justify-content-between">
        <div class="media align-items-center m-b-15">
            <div class="avatar avatar-image rounded" style="height: 70px; width: 70px">
                <img src="../../assets/<?php echo $row['item_image']; ?>" alt="">
            </div>
            <div class="m-l-15">
                <h4 class="m-b-0"><?php echo $row['item_name']; ?></h4>
                <p class="text-muted m-b-0">Item Code: #<?php echo $itemNumber; ?></p>
                <p class="text-muted m-b-0">Shelf Code: #<?php echo $stockDetails[0]['shelf_num'];; ?></p>
                <p class="text-muted m-b-0">Unit Price: $<?php echo $stockDetails[0]['primary_unit_price']; ?></p>
            </div>
        </div>
        <div class="m-b-15">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                Get Items
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Model1">
                Edit Threshold
            </button>
            <div class="modal fade" id="exampleModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Get Some Items</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <i class="anticon anticon-close"></i>
                            </button>
                        </div>
                        <form id="myForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                            <div class="modal-body">
                                <input type="hidden" name="itemNumber" value="<?php echo $itemNumber; ?>">
                                <label for="qty">Enter Quantity: </label>
                                <input type="number" name="qty" placeholder="1" min="1" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="submitForm()">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
        <div class="modal fade" id="Model1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Threshold</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="anticon anticon-close"></i>
                        </button>
                    </div>
                    <form id="myForm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                        <div class="modal-body">
                            <input type="hidden" name="itemNumber" value="<?php echo $itemNumber; ?>">
                            <label for="threshold">Enter Threshold: </label>
                            <input type="number" name="threshold" placeholder="1" min="1" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="submitForm1()">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
    </div>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#product-overview">Overview</a>
        </li>
    </ul>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="myTable" class="table table-hover text-center mx-auto">
                <thead>
                    <tr>
                        <th scope=" col">#</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($stockDetails as $item) {
                        $i++;
                        echo '<tr>
                            <th scope="row">' . $i . '</th>
                            <td>' . $item['primary_quantity_batch'] . '</td>
                            <td>' . $item['item_expiry_date'] . '</td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-5">
        <h3>Price Fluctuation</h3>
        <div>
            <?php
            $json = json_decode(fluctuationPrice($con, $itemNumber), true);
            echo '
            <script src="../../public/assets/vendors/chartist/chartist.min.js"></script>
            <div class="ct-chart" id="simple-line-chart"></div>
            <script>
                new Chartist.Line("#simple-line-chart", {
                    labels: [';
            foreach ($json['prices'] as $key => $value) {
                echo '"' . $key . '",';
            }
            echo '],
                    series: [
                        [';
            foreach ($json['prices'] as $key => $value) {
                echo $value . ',';
            };
            echo '],
                    ]
                }, {
                    fullWidth: true,
                    chartPadding: {
                        right: 40
                    }
                });
            </script>'
            ?>
        </div>
    </div>
    <div class="col-5 offset-1">
        <h3>
            Expired Items
        </h3>
        <div>
            <?php
            $json = json_decode(getExpiredItems($con, $itemNumber), true);
            $json1 = json_decode(getQuantityItems($con,$itemNumber),true);
            echo '
                <script src="../../public/assets/vendors/chartist/chartist.min.js"></script>
                <div class="ct-chart" id="stacked-bar"></div>
                <script>
                    new Chartist.Bar("#stacked-bar", {
                        labels: [';
                        foreach ($json as $month=>$val) {
                echo '"' . $month .
                '",';
                };
                echo '],
                        series: [[';
                foreach ($json as $month=>$key) {
                    echo $json[$month] . ',';
                };
                echo '],[';
                foreach($json1 as $month=>$key){
                    echo $json1[$month].',';
                }
                echo']]
                    }, {
                        stackBars: true,
                        axisY: {
                            labelInterpolationFnc: function(value) {
                                return (value);
                            }
                        }
                    }).on("draw", function(data) {
                        if(data.type === "bar") {
                            data.element.attr({
                                style: "stroke-width: 30px"
                            });
                        }
                    });
                </script>'
            ?>
        </div>
    </div>
</div>
<?php
include('../layouts/boilerplate_footer.php');
?>
<script>
    function submitForm() {
        document.getElementById("myForm").submit();
    }

    function submitForm1() {
        document.getElementById("myForm1").submit();
    }
</script>