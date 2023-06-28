<?php
    session_start();
    session_destroy();
    header('location:http://localhost/fyp_project/views/users/login.php');
?>