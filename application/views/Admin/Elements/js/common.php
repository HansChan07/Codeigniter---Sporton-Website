<?php
$common_js = array(
					'admin/js/jquery.min.js',
					'admin/js/jquery-ui-1.10.3.min.js',
					'admin/js/bootstrap.min.js',
					'admin/js/jquery.validate.js',
					'admin/js/app.js'
				);
add_common_js($common_js);
echo put_headers_js();		
if(!empty($pageJSPath) && $pageJSPath!=""){
	$this->load->view($pageJSPath); 
}
