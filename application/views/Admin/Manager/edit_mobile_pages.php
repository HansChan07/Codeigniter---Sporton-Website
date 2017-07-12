<aside class="right-side">
	<section class="content-header">
        <h1>Mobile Pages <small>Edit mobile pages</small></h1>
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
		<form  action="<?php echo base_url().'admin/manager/edit_mobile_pages'; ?>" method="post" id="editPages">
			<div class="form-group">
                <label>Terms and Conditions *</label>
				<textarea  name="terms_condition" rows="5" cols="44" class="required form-control"><?php echo (!empty($terms_condition))?$privacy_policy:''; ?></textarea>
            </div>
			<div class="form-group">
                <label>Privacy Policy *</label>
				<textarea  name="privacy_policy" rows="5" cols="44" class="required form-control"><?php echo (!empty($privacy_policy))?$privacy_policy:''; ?></textarea>
            </div>
			<div class="form-group">			
				<input class="btn btn-primary btn-sm" type="Submit" value="Submit">
			</div>
		</form>
	</section>
</aside>
<script src="<?php echo globel_url .'assets/admin/js/ckeditor/ckeditor.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() { 
	var validator = $("#editPages").click(function(){
		}).validate({
					ignore: "",
					rules:
					{
						"terms_condition":{ required:true,}
						"privacy_policy":{ required:true,}
					},
				   messages:
				   {
						"terms_condition": { required:'This field is required'},
						"privacy_policy": { required:'This field is required'},
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