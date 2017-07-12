	<div class="form-box" id="login-box">
            <div class="header">Login</div>
            <form action="<?php echo base_url().'admin/auth/login'; ?>" method="post" id="adminLogin">
                <div class="body bg-gray">
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
				<?php if(validation_errors()!=""){ echo validation_errors(); } ?>
					<div class="form-group">
                        <input type="text" name="uemail" class="form-control required" placeholder="Email"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="upassword" class="form-control required" placeholder="Password"/>
                    </div>          
                    <div class="form-group">
						<input type="checkbox" name="remember_me" checked /> Remember me
                    </div>
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block">Submit</button>
					<p><a href="<?php echo base_url().'admin/auth/forgotPassword' ?>">I forgot my password</a></p>
                </div>
            </form>
      </div>
<script type="text/javascript" >
$(document).ready(function(){
	$("#adminLogin").validate({
		rules: { 
			'uemail': { email: true }
		},
			
	});
});
</script>