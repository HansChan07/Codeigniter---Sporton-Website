<aside class="right-side">
	<section class="content-header">
        <h1>Sports Categories <small>Add category</small></h1>
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
			<?php if(validation_errors()!=""){ echo validation_errors(); } ?>
	</div>
	<section class="col-md-8">
		<form  action="<?php echo base_url().'admin/event/add_category'; ?>" method="post" id="addCategory">
			<div class="form-group">
                <label>Category Name *</label>
                 <input type="text" class="form-control required" name="category_name" placeholder="Category Name" />
            </div>
			<div class="form-group">
                <label>Category Status *</label>
				 <select class="form-control" name="category_status">
					<option value="1" >Active</option>
                    <option value="0" >Inactive</option>
                 </select>
            </div>
			<div class="form-group">			
				<input class="btn btn-primary btn-sm" type="Submit" value="Submit">
				<a href="<?php echo base_url().'admin/event/categories' ?>"  class="btn btn-success btn-sm backbtn" >Back</a>
			</div>
		</form>
	</section>
</aside>
<script type="text/javascript">
$(document).ready(function() { 
	$("#addCategory").validate();
});	
</script>