<aside class="right-side">
	<section class="content-header">
        <h1>Events <small>Manage events</small></h1>
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
				<h3 class="box-title">Events</h3>
            </div>
            <div class="box-body table-responsive">
				<table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
							<th>SNo.</th>
                            <th>Title</th>
							<th>Category</th>
							<th>Added By</th>
							<th>Location</th>
							<th>Star Date/Time</th>
							<th>End Date/Time</th>
							<th>Status</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $i = 1; foreach($events as $event) {  ?>
						<tr>
                            <td><?php echo $i; ?></td>
							<td><?php echo $event->event_title; ?></td>
							<td><?php echo $event->category_name; ?></td>
							<td><?php echo $event->user_name; ?></td>
							<td><?php echo $event->event_location; ?></td>
							<td><?php echo $event->start_date; ?></td>
							<td><?php echo $event->end_date; ?></td>
                            <td><?php echo ($event->event_status==1)?'Active':'Inactive'; ?></td>
							<td>
								<a title="Delete" href="<?php echo base_url()."admin/event/delete_event/".base64_encode(convert_uuencode($event->event_id));?>" class="btn btn-success btn-sm" style="margin-top:10px;" onclick="return confirm('Are you sure?');">											
									<i class="fa fa-edit">&nbsp; Delete</i>
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