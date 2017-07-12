<?php $controller = $this->router->fetch_class(); ?>	
	<aside class="left-side sidebar-offcanvas" style="min-height: 500px;">
		<section class="sidebar">
			<div class="user-panel">
				<div class="pull-left image">
					<img src="<?php echo globel_url .'uploads/admin/thumb/'. $this->session->userdata('admin_image'); ?>" class="img-circle" />
				</div>
				<div class="pull-left info">
					<p>Hello, <?php echo $this->session->userdata('admin_name'); ?></p>
				</div>
			</div>
			<ul class="sidebar-menu">
				<li class="<?php echo (preg_match("/profile/",$controller))?'active':''; ?>">
					<a href="<?php echo base_url().'admin/profile/dashboard'; ?>">
						<i class="fa fa-dashboard"></i> <span>Dashboard</span>
					</a>
				</li>
				<li class="treeview <?php echo (preg_match("/manager/",$controller))?'active':''; ?>">
					<a href="javascript:void(0);">
						<i class="fa fa-bar-chart-o"></i>
						<span>Management</span>
						<i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						<li><a href="<?php echo base_url().'admin/manager/edit_settings'; ?>"><i class="fa fa-angle-double-right"></i> Site Settings</a></li>
						<li><a href="<?php echo base_url().'admin/manager/email_templates'; ?>"><i class="fa fa-angle-double-right"></i> Email Templates</a></li>
						<li><a href="<?php echo base_url().'admin/manager/edit_mobile_pages'; ?>"><i class="fa fa-angle-double-right"></i> Mobile Pages</a></li>
					</ul>
				</li>
				<li class="treeview <?php echo (preg_match("/event/",$controller))?'active':''; ?>">
					<a href="javascript:void(0);">
						<i class="fa fa-laptop"></i>
						<span>Events</span>
						<i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						<li><a href="<?php echo base_url().'admin/event/categories'; ?>"><i class="fa fa-angle-double-right"></i>Sports Categories</a></li>
						<li><a href="<?php echo base_url().'admin/event/events'; ?>"><i class="fa fa-angle-double-right"></i>Events</a></li>
					</ul>
				</li>
				<li class="<?php echo (preg_match("/user/",$controller))?'active':''; ?>">
					<a href="<?php echo base_url().'admin/user/users'; ?>">
						<i class="fa fa-user"></i> <span>Users</span>
					</a>
				</li>
			</ul>
		</section>
	</aside>