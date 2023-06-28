<?php
include("../../controllers/users/login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link href="../../public/assets/css/app.min.css" rel="stylesheet">
</head>

<body>
    <div class="app">
        <div class="container-fluid p-0 h-100">
            <div class="row no-gutters h-100 full-height">
                <div class="col-lg-4 d-none d-lg-flex bg" style="background-image:url('../../public/assets/images/others/login-1.jpg')">
                    <div class="d-flex h-100 p-h-40 p-v-15 flex-column justify-content-between">
                        <div>
                            
                        </div>
                        <div>
                            <h1 class="text-white m-b-20 font-weight-normal">Stock Management</h1>
                            <p class="text-white font-size-16 lh-2 w-80 opacity-08">Stock management is the process of managing the goods your business plans to sell. This involves acquiring, storing, organising and tracking those goods. Stock management also involves keeping records of changes in your inventory over time.</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    
                                </li>
                                <li class="list-inline-item">
                                    
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 bg-white">
                    <div class="container h-100">
                        <div class="row no-gutters h-100 align-items-center">
                            <div class="col-md-8 col-lg-7 col-xl-6 mx-auto">
                                <h2>Sign In</h2>
                                <p class="m-b-30">Enter your credential to get access</p>
                                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="userName">Username:</label>
                                        <div class="input-affix">
                                            <i class="prefix-icon anticon anticon-user"></i>
                                            <input type="text" class="form-control" id="userName" placeholder="Username" name="username_OR_email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="password">Password:</label>
                                        <div class="input-affix m-b-10">
                                            <i class="prefix-icon anticon anticon-lock"></i>
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <button class="btn btn-primary">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Core Vendors JS -->
    <script src="../../public/assets/js/vendors.min.js"></script>

    <!-- page js -->

    <!-- Core JS -->
    <script src="../../public/assets/js/app.min.js"></script>

</body>

</html>