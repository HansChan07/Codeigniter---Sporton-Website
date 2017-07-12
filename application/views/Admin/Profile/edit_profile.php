<aside class="right-side">
	<section class="content-header">
        <h1>Profile <small>Edit profile</small></h1>
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
 	<div class="col-md-6" id="login-box">
		<h4 class="box-title">Edit Your Profile</h4>
			<div class="body bg-gray edit_form_padding">	
				<form method="post" action="<?php echo base_url().'admin/profile/edit_profile' ?>" enctype="multipart/form-data" id="editProfile">           
                    <div class="form-group">
                      Name: *<input type="text" class="form-control required" name="admin_name" value="<?php echo (isset($adminData->admin_name)?$adminData->admin_name:'');?>" placeholder="Full name"/>
                    </div> 
                    <div class="form-group">
                       Email: *<input type="email" class="form-control required" name="admin_email" value="<?php echo (isset($adminData->admin_email)?$adminData->admin_email:'');?>" palceholder="Email address" />
                    </div>
					<div class="form-group">
                      Phone: *<input type="text" class="form-control required" name="admin_mobile" value="<?php echo (isset($adminData->admin_mobile)?$adminData->admin_mobile:'');?>" placeholder="Mobile number" onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" minlength='10' />
                    </div>
					<div class="form-group"> 
						<label>Profile Picture</label>
						 <input type="file" class="img_ext" name="admin_image" accept="image/*" />
					</div>
			        <div class="form-group"> 
			          <img src="<?php echo globel_url .'uploads/admin/thumb/'.(!empty($adminData->admin_image)?$adminData->admin_image:'nopic.jpg'); ?>">
			        </div>
                    <button type="submit" class="btn bg-olive btn-block edit_submit">Submit </button> 
					<p><a href="<?php echo base_url().'admin/profile/change_password' ?>" class="chgPwd">Change Password</a></p>
				</form>
			</div>
</aside>
<script type="text/javascript">
$(document).ready(function() { 
	$('#editProfile').validate();
	jQuery.validator.addMethod('img_ext',function(value,element){ 
		if(value==""){
			return true;
		}
		var ext_index = value.lastIndexOf('.');
		var ext = value.substring(ext_index+1);
		var ext_lcase = ext.toLowerCase();
		if(ext_lcase == 'jpeg' || ext_lcase == 'jpg' || ext_lcase == 'png' || ext_lcase == 'gif') {
			return true;
		} else {
			return false;
		} 
	}, "Please select valid format file (like jpeg, png & gif)"); 
	
});
</script>