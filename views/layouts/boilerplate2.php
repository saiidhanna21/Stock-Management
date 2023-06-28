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
			<?php include('../../utils/connect.php');?>
			<?php include('../partials/header.php') ?> <?php include('../partials/sideNavbar.php') ?>
			<div class="page-container">
				<div class="main-content">
					<?php include('../partials/flash.php') ?>