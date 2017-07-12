<aside class="right-side">
	<section class="content-header">
        <h1>Sports Categories <small>Manage sports categories</small></h1>
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
				<h3 class="box-title">Sports Categories</h3>
				<a href="<?php echo base_url().'admin/event/add_category'; ?>" class="btn btn-success btn-sm addbtn" >Add New</a>
            </div>
            <div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
							<th>SNo.</th>
                            <th>Name</th>
							<th>Status</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $i = 1; foreach($categories as $category) {  ?>
						<tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo (!empty($category->category_name))?$category->category_name:''; ?></td>
                            <td><?php echo ($category->category_status==1)?'Active':'Inactive'; ?></td>
							<td><a title="Edit" href="<?php echo base_url()."admin/event/edit_category/".base64_encode(convert_uuencode($category->category_id));?>" class="btn btn-success btn-sm">											
									<i class="fa fa-edit">&nbsp; Edit</i>
								</a>
							</td>
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