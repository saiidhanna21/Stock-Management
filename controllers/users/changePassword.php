<?php
if (!empty($_POST["oldPass"]) && !empty($_POST["newPass"]) && !empty($_POST["confPass"])) {
    extract($_POST);
    $query = "SELECT * FROM `admin`;";
    $result = mysqli_fetch_assoc(mysqli_query($con, $query));
    if (password_verify($oldPass, $result['password'])){
        if(strlen($newPass)<6){
            echo flash('Your Password Must Contain At Least 6 Characters!', true);
        }else{
        if($newPass==$confPass){
        if($oldPass==$newPass){
            echo flash('Please choose a new password not used before!', true);
        }else{
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $query = "UPDATE `admin` SET `password`='$hashed';";
            $result = mysqli_query($con, $query);
            echo flash('Your Password Changed Succefully!', false);
        }
      }else{
        echo flash('Your New Password Is Not The Same Like The Confirm Password', true);
      }
    }
}else{
        echo flash('Your old password is incorrect!', true);
    }}

?>