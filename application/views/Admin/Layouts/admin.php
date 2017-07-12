<!DOCTYPE html>
<html class="bg-black">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<?php $this->load->view('Admin/Elements/css/common'); ?>
<?php $this->load->view('Admin/Elements/js/common'); ?>
<script type="text/javascript">
	var base_url = '<?php echo base_url(); ?>';
</script>
</head>
<body class="skin-blue" style="min-height: 800px;">
	<?php $this->load->view('Admin/Elements/header'); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php  $this->load->view('Admin/Elements/sidebar'); ?>
		<?php if(!empty($pagePath) && $pagePath!=""){ 
				$this->load->view($pagePath); 
		}
		?>
	</div>
</body>
</html>
