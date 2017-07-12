	<aside class="right-side">
        <section class="content-header">
             <h1>Dashboard <small>Control panel</small></h1>
        </section>
		<div class="alert-div">
			<?php if($this->session->flashdata('error')): ?>
				<div class="alert alert-danger alert-dismissable">
					<i class="fa fa-ban"></i>
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
					<b>Error! </b><?php echo $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>
			<?php if($this->session->flashdata('success')):?>
				<div class="alert alert-success alert-dismissable">
					<i class="fa fa-check"></i>
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
					<b>Success! </b><?php echo $this->session->flashdata('success'); ?></p>
				</div>
			<?php endif; ?>
		</div>
        <section class="content">
            <div class="row">
                <div class="col-lg-3 col-xs-6">
					<div class="small-box bg-aqua">
                       <div class="inner"><h3><?php echo $stats['totalUsers']; ?></h3>
                          <p>Users</p>
                       </div>
                       <div class="icon">
                         <i class="ion ion-bag"></i>
                       </div>
                       <a href="<?php echo base_url().'admin/user/users'; ?>" class="small-box-footer">
                          More info <i class="fa fa-arrow-circle-right"></i>
                       </a>
                    </div>
                </div>
				<div class="col-lg-3 col-xs-6">
					<div class="small-box bg-green">
                       <div class="inner"><h3><?php echo $stats['totalEvents'] ?></h3>
                          <p>Events</p>
                       </div>
                       <div class="icon">
                         <i class="ion ion-stats-bars"></i>
                       </div>
                       <a href="<?php echo base_url().'admin/event/events'; ?>" class="small-box-footer">
                          More info <i class="fa fa-arrow-circle-right"></i>
                       </a>
                    </div>
                </div>
           </div>
		</section>
   </aside>