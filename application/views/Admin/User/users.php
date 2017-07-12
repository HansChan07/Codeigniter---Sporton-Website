<aside class="right-side">
	<section class="content-header">
        <h1>Users <small>Manage users</small></h1>
    </section>
	<div class="alert-div">
			<?php if($this->session->flashdata('error')): ?>
				<div class="alert alert-danger alert-dismissable">
					<i class="fa fa-ban"></i>
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006 </button>
					<b>Error! </b><?php echo $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>
			<?php if($this->session->flashdata('success')):?>
				<div class="alert alert-success alert-dismissable">
					<i class="fa fa-check"></i>
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006 </button>
					<b>Success! </b><?php echo $this->session->flashdata('success'); ?></p>
				</div>
			<?php endif; ?>
	</div>
	<section class="content">
	    <div class="box">
            <div class="box-header">
				<h3 class="box-title">Users</h3>
            </div>
            <div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
							<th>SNo.</th>
                            <th>Name</th>
							<th>Gender</th>
							<th>Email</th>
							<th>Location</th>
							<th>Image</th>
							<th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $i = 1; foreach($users as $user) {  ?>
						<tr>
                            <td><?php echo $i; ?></td>
							<td><?php echo $user->user_name; ?></td>
							<td><?php echo ($user->user_sex)?'Male':'Female'; ?></td>
							<td><?php echo $user->user_email; ?></td>
							<td><?php echo $user->user_location; ?></td>
							<td><img src="<?php echo globel_url .'uploads/mobile/thumb/'. (!empty($user->user_image)?$user->user_image:'default_pic.jpeg'); ?>" style="height:100px;width:100px;" /></td>
                            <td><?php echo ($user->user_status==1)?'Active':'Inactive'; ?></td>
                        </tr>
						<?php $i++; } ?>
                    </table>
            </div>
		</div>
	</section>
</aside>
<script type="text/javascript">
$(function() {
    $("#example1").dataTable();
});
</script>