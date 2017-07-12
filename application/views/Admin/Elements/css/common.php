<?php
$common_css = array(
				'admin/css/bootstrap.min.css',
				'admin/css/font-awesome.min.css',
				'admin/css/ionicons.min.css',
				'admin/css/AdminLTE.css',
				'admin/css/developer.css'
			);
add_common_css($common_css);
echo put_headers_css();	
if(!empty($pageCSSPath) && $pageCSSPath!=""){
	$this->load->view($pageCSSPath); 
}



