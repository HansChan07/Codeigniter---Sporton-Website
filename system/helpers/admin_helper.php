<?php 
if(!function_exists('sendEmail')){
    function sendEmail($to,$template_slug,$replace_vars = array()) {
		$ci = &get_instance();
		$query = $ci->db->get_where('tbl_email_templates', array('etpl_slug' =>$template_slug));
		$template = $query->first_row();
		if(!empty($template) && !empty($template->etpl_subject) && !empty($template->etpl_body) && $template->etpl_status == 1) {
			$subject = $template->etpl_subject;
			$body = $template->etpl_body;
		} else {
			$template_title = $template->etpl_subject;
			$query = $ci->db->get_where('tbl_email_templates', array('etpl_slug' =>'default_notification'));
			$default = $query->first_row();
			$subject = $default->etpl_subject;
			$body = $default->etpl_body;
			//admin name
			$query = $ci->db->get_where('tbl_admin',array('admin_id'=>1));
			$adminData = $query->first_row();
			$replace_vars = array(
							'{username}'=>$adminData->admin_name,
							'{message}'=>"There is problem with the email template having subject ($template_title) . Please review in admin panel for this.",
						);
			$to = CONF_NOTIFICATION_EMAIL;
		}
		$replace_vars['{url}'] = globel_url;
		$replace_vars['{sitename}'] = CONF_WEBSITE_NAME;
		foreach($replace_vars as $key => $val) {
			$body = str_replace($key, $val, $body);
		} 
		if(CONF_SMTP_EMAIL == 1) {
			$ci->load->library('email');
			$config['protocol'] = "smtp";
			$config['smtp_host'] = CONF_SMTP_HOST;  // "ssl://smtp.gmail.com"
			$config['smtp_port'] = CONF_SMTP_PORT;  //"465"
			$config['smtp_user'] = CONF_SMTP_USERNAME; 
			$config['smtp_pass'] = CONF_SMTP_PASSWORD;
			$config['charset'] = "utf-8";
			$config['mailtype'] = "html";
			$config['newline'] = "\r\n";
			$ci->email->initialize($config);
			$ci->email->from(CONF_EMAILS_FROM,CONF_WEBSITE_NAME);
			$list = array($to);
			$ci->email->to($list);
			//$this->email->reply_to();
			$ci->email->subject($subject);
			$ci->email->message($body);
			$ci->email->send();
		} else {
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ' . CONF_EMAILS_FROM . "\r\n";
			mail($to, $subject, $body, $headers);
		
		}
		return true;
	}
}

if(!function_exists('getRandomPassword')){
	function getRandomPassword($n){
		$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass='';
		for($i=0; $i<$n; $i++){
			$pass .= substr($chars,rand(0, strlen($chars)-1), 1);
		}
		return $pass;
	}
}

if(!function_exists('createUniqueSlug')){
	function createUniqueSlug($string){
		$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		$ci = &get_instance();
		$query = $ci->db->get_where('tbl_pages', array('tbl_slug' =>$slug));
		if($query->num_rows() > 0){
			$num = $query->num_rows();
			return createUniqueSlug($string.'-'.$num);
		}
		return $slug;
	}
}