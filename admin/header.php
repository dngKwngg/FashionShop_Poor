<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if(!isset($_SESSION['user'])) {
	header('location: login.php');
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/jquery.fancybox.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">
	<link rel="stylesheet" href="css/on-off-switch.css"/>
	<link rel="stylesheet" href="css/summernote.css">
	<link rel="stylesheet" href="style.css">

</head>

<body class="hold-transition fixed skin-blue sidebar-mini">

	<div class="wrapper">

		<header class="main-header">

			<a href="index.php" class="logo">
				<span class="logo-lg">N10Shop</span>
			</a>

			<nav class="navbar navbar-static-top">
				
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>

				<span style="float:left;line-height:50px;color:#fff;padding-left:15px;font-size:18px;">Admin</span>
    <!-- Top Bar ... User Inforamtion .. Login/Log out Area -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../assets/uploads/<?php echo $_SESSION['user']['photo']; ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['user']['full_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Chỉnh sửa hồ sơ</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Đăng xuất</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</nav>
		</header>

  		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>
<!-- Side Bar to Manage Shop Activities -->
  		<aside class="main-sidebar">
    		<section class="sidebar">
      
      			<ul class="sidebar-menu">

			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-hand-o-right"></i> <span>Tổng quan</span>
			          </a>
			        </li>

                    <li class="treeview <?php if( ($cur_page == 'size.php') || ($cur_page == 'size-add.php') || ($cur_page == 'size-edit.php') || ($cur_page == 'color.php') || ($cur_page == 'color-add.php') || ($cur_page == 'color-edit.php') || ($cur_page == 'top-category.php') || ($cur_page == 'top-category-add.php') || ($cur_page == 'top-category-edit.php') ) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-hand-o-right"></i>
                            <span>Thiết lập</span>
                            <span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="size.php"><i class="fa fa-circle-o"></i> Size</a></li>
                            <li><a href="color.php"><i class="fa fa-circle-o"></i> Màu</a></li>
                            <li><a href="top-category.php"><i class="fa fa-circle-o"></i> Danh mục</a></li>
                        </ul>
                    </li>


                    <li class="treeview <?php if( ($cur_page == 'product.php') || ($cur_page == 'product-add.php') || ($cur_page == 'product-edit.php') ) {echo 'active';} ?>">
                        <a href="product.php">
                            <i class="fa fa-hand-o-right"></i> <span>Quản lý sản phẩm</span>
                        </a>
                    </li>


                    <li class="treeview <?php if( ($cur_page == 'order.php') ) {echo 'active';} ?>">
                        <a href="order.php">
                            <i class="fa fa-hand-o-right"></i> <span>Quản lý đơn hàng</span>
                        </a>
                    </li>


                     <li class="treeview <?php if( ($cur_page == 'slider.php') ) {echo 'active';} ?>">
			          <a href="slider.php">
			            <i class="fa fa-hand-o-right"></i> <span>Thanh trượt</span>
			          </a>
			        </li>
                    <!-- Icons to be displayed on Shop -->

					<li class="treeview <?php if( ($cur_page == 'customer.php') || ($cur_page == 'customer-add.php') || ($cur_page == 'customer-edit.php') ) {echo 'active';} ?>">
			          <a href="customer.php">
			            <i class="fa fa-hand-o-right"></i> <span>Khách hàng</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'page.php') ) {echo 'active';} ?>">
			          <a href="page.php">
			            <i class="fa fa-hand-o-right"></i> <span>Giới thiệu</span>
			          </a>
			        </li>

					<li class="treeview <?php if( ($cur_page == 'service.php') ) {echo 'active';} ?>">
			          <a href="service.php">
			            <i class="fa fa-hand-o-right"></i> <span>Dịch vụ</span>
			          </a>
			        </li>


      			</ul>
    		</section>
  		</aside>

  		<div class="content-wrapper">