<aside class="right-side">
	<section class="content-header">
        <h1>Settings <small>Edit settings</small></h1>
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
	<section class="col-md-6">
		<form  action="<?php echo base_url().'admin/manager/edit_settings'; ?>" method="post" id="editSetting" enctype="multipart/form-data">
			<div class="form-group">
                <label>Website Mode *</label>
				 <select class="form-control" name="conf_website_mode">
                    <option value="1" <?php if(CONF_WEBSITE_MODE==1) { echo 'selected'; } ?>>Up</option>
					<option value="2" <?php if(CONF_WEBSITE_MODE==2) { echo 'selected'; } ?>>Down</option>
					<option value="3" <?php if(CONF_WEBSITE_MODE==3) { echo 'selected'; } ?>>Maintenance</option>					
                 </select>
            </div>
			<div class="form-group">
                <label>Website Name *</label>
                 <input type="text" class="form-control required" name="conf_website_name" value="<?php echo CONF_WEBSITE_NAME; ?>" />
            </div>
			<div class="form-group">
                <label>Emails From *</label>
                 <input type="email" class="form-control required" name="conf_emails_from" value="<?php echo CONF_EMAILS_FROM; ?>" />
            </div>
			<div class="form-group">
                <label>Notification Emails To *</label>
                 <input type="email" class="form-control required" name="conf_notification_email" value="<?php echo CONF_NOTIFICATION_EMAIL; ?>" />
            </div>
			<div class="form-group">
                <label>Page Size *</label>
                 <input type="text" class="form-control required" name="conf_page_size" value="<?php echo CONF_PAGE_SIZE; ?>"  onkeyup="this.value=this.value.replace(/[^0-9.]/g,'');" min="1" />
            </div>
			<div id="smtp_details" style="<?php if(CONF_SMTP_EMAIL==0) { echo 'display:none'; } ?>">
				<div class="form-group">
					<label>SMTP Server *</label>
					<input type="text" class="form-control smtp_feild <?php if(CONF_SMTP_EMAIL==1) { echo 'required'; } ?> " name="conf_smtp_host" value="<?php echo CONF_SMTP_HOST; ?>" />
				</div>
				<div class="form-group">
					<label>SMTP Port *</label>
					<input type="text" class="form-control smtp_feild <?php if(CONF_SMTP_EMAIL==1) { echo 'required'; } ?> " name="conf_smtp_port" value="<?php echo CONF_SMTP_PORT; ?>" />
				</div>
				<div class="form-group">
					<label>SMTP User Name *</label>
					<input type="text" class="form-control smtp_feild <?php if(CONF_SMTP_EMAIL==1) { echo 'required'; } ?> " name="conf_smtp_username" value="<?php echo CONF_SMTP_USERNAME; ?>" />
				</div>
				<div class="form-group">
					<label>SMTP Password *</label>
					<input type="password" class="form-control smtp_feild  <?php if(CONF_SMTP_EMAIL==1) { echo 'required'; } ?> " name="conf_smtp_password" value="<?php echo CONF_SMTP_PASSWORD; ?>" />
				</div>
			</div>
			<div class="form-group"> 
				<label>Website Logo *</label>
				<input type="file" class="img_ext" name="cms_image" accept="image/*" />
			</div>
			<div class="form-group"> 
			   <img src="<?php echo globel_url .'assets/website/thumb/'. CONF_WEBSITE_LOGO; ?>">
			</div>
			<div class="form-group">			
				<input class="btn btn-success btn-sm" type="Submit" value="Submit">
			</div>
		</form>
	</section>
</aside>
<script type="text/javascript">
$(document).ready(function() { 
	$('#editSetting').validate();
	$(".conf_checkbox").change(function(){
		var smtp = $(this).val();
		if(smtp==1) {
			$("#smtp_details").show();
			$(".smtp_feild").addClass('required');
		} else {
			$("#smtp_details").hide();
			$(".smtp_feild").removeClass('required');
		}
	});
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
