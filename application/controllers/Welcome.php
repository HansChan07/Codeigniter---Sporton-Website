<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function index() {
		$this->load->model('admin/event_model');
		$data['categories'] = $this->event_model->findAllCategories();
		$this->load->view('welcome_message',$data);
	}
	function send_message() {
		if($this->input->post()){
			$this->form_validation->set_rules('name', '', 'trim|required');
			$this->form_validation->set_rules('message', '', 'trim|required');	
			$this->form_validation->set_rules('email', '', 'trim|required|valid_email');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$html = 'You have contact use request from website. <br /><br />Details given below<br /><br/>User Name: '.$post["name"].'<br/ ><br />User Email: '.$post["email"].'<br /><br />';
				if(!empty($post['sports'])) {
					$i = 1;
					foreach($post['sports'] as $sport) {
						$html .= $i. '. '.$sport.'<br />';
						$i++;
					}
				}
				$html .= '<br />Message: '. $post["message"].'<br />';
				$query = $this->db->get_where('tbl_admin',array('admin_id'=>1));
				$adminData = $query->first_row();
				sendEmail(CONF_NOTIFICATION_EMAIL,'contact_us_request',array("{username}"=>$adminData->admin_name,"{html}"=>$html));
				echo 1;
				die;
			}
			echo 2;
			die;
		}
	}
}
