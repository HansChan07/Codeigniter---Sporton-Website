<aside class="right-side">
	<section class="content-header">
        <h1>Profile <small>Change password</small></h1>
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
        <h4 class="box-title">Change Your Password</h4>
			<div class="body bg-gray edit_form_padding">
				<form method="post" action="<?php echo base_url().'admin/profile/change_password'; ?>"  id="changePsw" >
                    <div class="form-group">
                      Current Password: *<input type="password" id="oldPass" class="form-control required" name="upassword" placeholder="Old Password" />
                    </div>
                    <div class="form-group">
                       New Password: *<input type="password"  class="form-control required" name="new_password" id="newPass" placeholder="New Password"/>
                    </div>
                    <div class="form-group">
                       Confirm Password: *<input type="password" class="form-control required" name="confirm_password" id="confPass" placeholder="Confirm Password" />
                    </div>
                   <button type="submit" class="btn bg-olive btn-block edit_submit">Submit </button> 
				   <p><a href="<?php echo base_url().'admin/profile/edit_profile' ?>" class="chgPwd">Go Back to Profile</a></p>
				</form>
		</div>
	</div>
</aside>
<script type="text/javascript">
$(document).ready(function(){
    $('#changePsw').validate(); 
	$("#confPass").rules('add',{equalTo: "#newPass",
	   messages: {equalTo: "New password and confirm password field doesn't match."}});
});
</script>
