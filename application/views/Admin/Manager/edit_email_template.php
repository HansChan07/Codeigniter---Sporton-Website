<aside class="right-side">
	<section class="content-header">
        <h1>Email Templates <small>Edit email templates</small></h1>
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
		<form  action="<?php echo base_url().'admin/manager/edit_email_template/'.base64_encode(convert_uuencode($template->etpl_id)); ?>" method="post" id="editTemplate">
			<div class="form-group">
                <label>Subject *</label>
                 <input type="text" class="form-control required" name="etpl_subject" value="<?php echo (!empty($template->etpl_subject))?$template->etpl_subject:''; ?>" />
            </div>
			<div class="form-group">
                <label>Status *</label>
				 <select class="form-control" name="etpl_status">
					<option value="1" <?php if($template->etpl_status==1) { echo 'selected'; } ?>>Active</option>
                    <option value="0" <?php if($template->etpl_status==0) { echo 'selected'; } ?>>Inactive</option>
                 </select>
            </div>
			<div class="form-group">
                <label>Body *</label>
				<textarea  name="etpl_body" rows="5" cols="44" class="required form-control ckeditor"><?php echo (!empty($template->etpl_body))?$template->etpl_body:''; ?></textarea>
            </div>
			<div class="form-group"> 
				<label>Replace Vars</label>
					<br>
					<?php echo (!empty($template->etpl_replace_vars))?$template->etpl_replace_vars:''; ?>
			</div>
			<div class="form-group">			
				<input class="btn btn-primary btn-sm" type="Submit" value="Submit">
				<a href="<?php echo base_url().'admin/manager/email_templates'; ?>"  class="btn btn-success btn-sm backbtn" >Back</a>
			</div>
		</form>
	</section>
</aside>
<script src="<?php echo globel_url .'assets/admin/js/ckeditor/ckeditor.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() { 
	var validator = $("#editTemplate").click(function(){
		}).validate({
					ignore: "",
					rules:
					{
						"etpl_body":{ required:true,}
					},
				   messages:
				   {
						"etpl_body": { required:'This field is required'},
			   		},
				errorPlacement: function(label, element) {
					if (element.is("textarea.ckeditor")) {
						label.insertAfter(element.next());
					} else {
						label.insertAfter(element)
					}
				}
		});
});	
</script>