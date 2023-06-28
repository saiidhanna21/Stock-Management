<div class="header">
	<div class="logo logo-dark">
		<h3 class="text-center mt-3">Stock Management</h3>
	</div>
	<div class="logo logo-white">
		<h3 class="text-center mt-3">Stock Management</h3>
	</div>
	<div class="nav-wrap">
		<ul class="nav-left">
			<li class="desktop-toggle">
				<a href="javascript:void(0);">
					<i class="anticon"></i>
				</a>
			</li>
			<li class="mobile-toggle">
				<a href="javascript:void(0);">
					<i class="anticon"></i>
				</a>
			</li>
		</ul>
		<ul class="nav-right">
			<li class="dropdown dropdown-animated scale-left">
				<div class="pointer" data-toggle="dropdown">
					<div class="avatar avatar-image m-h-10 m-r-15">
						<img src="http://localhost/fyp_project/public/assets/images/others/admin.png" alt="" />
					</div>
				</div>
				<div class="p-b-15 p-t-20 dropdown-menu pop-profile">
					<div class="p-h-20 p-b-15 m-b-10 border-bottom">
						<div class="d-flex m-r-50">
							<div class="avatar avatar-lg avatar-image">
								<img src="http://localhost/fyp_project/public/assets/images/others/admin.png" alt="" />
							</div>
							<div class="m-l-10">
								<p class="m-b-0 text-dark font-weight-semibold">Admin Admin</p>
								<p class="m-b-0 opacity-07">Stock Manager</p>
							</div>
						</div>
					</div>
					<a href="javascript:void(0);" class="dropdown-item d-block p-h-15 p-v-10">
						<div class="d-flex align-items-center justify-content-between">
							<form id="logoutForm" action="../users/logout.php">
								<div class="row">
									<div class="col-2">
										<i class="anticon opacity-04 font-size-16 anticon-logout"></i>
									</div>
									<div class="col-6 offset-2">
										<button class="btn btn-danger m-l-15" onclick="submitLogout()">Logout</button>
									</div>
								</div>
							</form>
						</div>
					</a>
				</div>
			</li>
			<li>
				<a href="javascript:void(0);" data-toggle="modal" data-target="#quick-view">
					<i class="anticon anticon-appstore"></i>
				</a>
			</li>
		</ul>
	</div>
</div>
<script>
	function submitLogout() {
		document.getElementById("logoutForm").submit();
	}
</script>