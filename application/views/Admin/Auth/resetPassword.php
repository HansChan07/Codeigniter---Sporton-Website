	<div class="form-box" id="login-box">
            <div class="header">Reset Password</div>
            <form action="<?php echo base_url().'admin/auth/resetPassword/'.$activation_key; ?>" method="post" id="changePwd">
                <div class="body bg-gray">
				<?php if($this->session->flashdata('formError')){ echo $this->session->flashdata('formError'); } ?>
					<div class="form-group">
                        <input id="new_password"  type="password" name="upassword" class="form-control required" placeholder="New Password"/>
                    </div>
                    <div class="form-group">
                        <input id="cnf_password" type="password" name="confpassword" class="form-control required" placeholder="Confirm Password"/>
                    </div>          
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block">Submit</button>
					<p>Login to your account <a href="<?php echo base_url().'admin/auth/login'; ?>">Click here</a></p>
                </div>
            </form>
      </div>
<script type="text/javascript" >
$(document).ready(function(){
	$("#changePwd").validate();
	$("#cnf_password").rules('add',{equalTo: "#new_password",
	   messages: {equalTo: "New password and confirm password doesn't match."}
	});
});
</script>