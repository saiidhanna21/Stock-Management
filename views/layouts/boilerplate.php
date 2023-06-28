<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Stock Management</title>
	<!-- Favicon -->
	<!-- page css -->
	<link href="http://localhost/fyp_project/public/assets/vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet" />
	<!-- Core css -->
	<link href="http://localhost/fyp_project/public/assets/css/app.min.css" rel="stylesheet" />
</head>

<body>
	<div class="app">
		<div class="layout">
			<?php include('../../utils/connect.php'); ?>
			<?php include('../partials/header.php');
			session_start();
			if (empty($_SESSION["loggedIn"])) {
				include('../partials/flash.php');
				$_SESSION["notLoggedIn"] = "true";
				$_SESSION["flash"] = flash("Unauthorized Access!! You must be logged in", true);
				header("location:http://localhost/fyp_project/views/users/login.php");
				exit();
			}
			include('../partials/sideNavbar.php') ?>
			<div class="page-container">
				<div class="main-content">
					<?php include('../partials/flash.php') ?>