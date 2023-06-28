<?php
include('../layouts/boilerplate.php');
include("../../controllers/users/changePassword.php");
?>
<?php
if (!empty($_POST['email'])) {
    $email = $_POST['email'];
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        $sql = "UPDATE `admin` SET `email`='$email' WHERE 1";
        mysqli_query($con, $sql);
        echo(flash('Your Email has been changed succefully',false));
    }else{
        echo (flash('Not a valid Email', true));
    }
}

?>
<div class="page-header no-gutters has-tab">
    <h2 class="font-weight-normal">Setting</h2>
</div>
<div class="container">
    <div class="tab-content m-t-15">
        <div class="tab-pane fade show active" id="tab-account">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setting Infomation</h4>
                </div>
                <div class="card-body">
                    <form id="myForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group col-md-9">
                                <button id="trigger-loading-1" class="btn btn-primary m-t-30" onclick="submitForm()">
                                    <i class="anticon anticon-loading m-r-5"></i>
                                    <i class="anticon anticon-poweroff m-r-5"></i>
                                    <span>Update</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Change Password</h4>
                </div>
                <div class="card-body">
                    <!-- CHANGE PASSWORD PHP CONTROL NEEDED -->
                    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="validated-form" novalidate>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="oldPassword">Old Password:</label>
                                <input type="password" class="form-control" id="oldPassword" minlength="6" name="oldPass" placeholder="Old Password" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="newPassword">New Password:</label>
                                <input type="password" class="form-control" id="newPassword" minlength="6" name="newPass" placeholder="New Password" required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-semibold" for="confirmPassword">Confirm Password:</label>
                                <input type="password" class="form-control" id="confirmPassword" minlength="6" name="confPass" placeholder="Confirm Password" required>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <button class="btn btn-primary m-t-30">Change</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
</script>
<script>
    $('#trigger-loading-1').on('click', function(e) {
        $(this).addClass("is-loading");
        setTimeout(function() {
            $("#trigger-loading-1").removeClass("is-loading");
        }, 1000);
        e.preventDefault();
    });
</script>