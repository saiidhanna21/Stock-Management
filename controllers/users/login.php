<?php 
include('../../utils/connect.php');
include('../../views/partials/flash.php');
include("../../controllers/stock/checkExpired.php");
checkExpiredItems($con);
session_start();
if (isset($_SESSION['flash'])) {
    echo $_SESSION['flash'];
    unset($_SESSION['flash']);
}
if (!empty($_POST)) {
    extract($_POST);
    if (!filter_var($username_OR_email, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT * FROM `admin` WHERE `username`='$username_OR_email';";
    } else {
        $query = "SELECT * FROM `admin` WHERE `email`='$username_OR_email';";
    }
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])){
            session_start();
            $_SESSION["loggedIn"] ="true"; 
            header('Location:../../views/stock/index.php');
            exit();
        }
        else{
       echo flash('Incorrect Username/Email or Password',True); 
    }
    }
    else{
        echo flash('Incorrect Username/Email or Password',True); 
     }
}
mysqli_close($con);
?>

