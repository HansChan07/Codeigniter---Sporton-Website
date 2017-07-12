<aside class="right-side">
	<section class="content-header">
        <h1>Email Templates <small>Manage email templates</small></h1>
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
				<h3 class="box-title">Email Template List</h3>
            </div>
            <div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
							<th>SNo.</th>
                            <th>Subject</th>
							<th>Status</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $i = 1; foreach($templates as $template) {  ?>
						<tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo (!empty($template->etpl_subject))?$template->etpl_subject:''; ?></td>
                            <td><?php echo ($template->etpl_status==1)?'Active':'Inactive'; ?></td>
							<td><a title="Edit" href="<?php echo base_url()."admin/manager/edit_email_template/".base64_encode(convert_uuencode($template->etpl_id));?>" class="btn btn-success btn-sm">											
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