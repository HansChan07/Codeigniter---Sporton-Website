<?php
class Manager extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('admin/manager_model');
		$logged_in = $this->session->userdata('logged_in');
		if(!$this->session->has_userdata('admin_id') || $logged_in == false) {
			redirect(base_url().'admin/auth/login');
		}
	}
	
	function edit_settings() {
		$admin_id = $this->session->userdata('admin_id');
		if($this->input->post()){
			$this->form_validation->set_rules('conf_website_mode', 'Website Mode', 'trim|required');
			$this->form_validation->set_rules('conf_website_name', 'Website Name', 'trim|required');
			$this->form_validation->set_rules('conf_emails_from', 'Emails From', 'trim|required|valid_email');
			$this->form_validation->set_rules('conf_notification_email', 'Notification Emails To', 'trim|required|valid_email');
			$this->form_validation->set_rules('conf_smtp_host', 'SMTP Server', 'trim|required');
			$this->form_validation->set_rules('conf_smtp_port', 'SMTP Port', 'trim|required');
			$this->form_validation->set_rules('conf_smtp_username', 'SMTP Username', 'trim|required');
			$this->form_validation->set_rules('conf_smtp_password', 'SMTP Password', 'trim|required');
			$this->form_validation->set_rules('conf_page_size', 'Page Size', 'trim|required');			
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				if(!empty($_FILES['conf_website_logo']['name']) && $_FILES['conf_website_logo']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES);
					if(empty($image_name)) {
						$this->session->set_flashdata('error','Error in image uploading! Please try again.');
						redirect(base_url().'admin/manager/edit_settings');
					}
				}
				$update = array(
							'conf_website_name' => $post['conf_website_name'],
							'conf_emails_from' => $post['conf_emails_from'],
							'conf_notification_email'  => $post['conf_notification_email'],
							'conf_smtp_email' => $post['conf_smtp_email'],
							'conf_smtp_host' => $post['conf_smtp_host'],
							'conf_smtp_port' => $post['conf_smtp_port'],
							'conf_smtp_username' => $post['conf_smtp_username'],
							'conf_smtp_password' => $post['conf_smtp_password'],
							'conf_website_logo' => ((!empty($image_name))?$image_name:CONF_WEBSITE_LOGO),
							'conf_website_mode'  => $post['conf_website_mode'],
							'conf_page_size' => $post['conf_page_size']
						);
				if($this->manager_model->updateConfig($update)){
					$this->session->set_flashdata('success',"Settings has been updated successfuly.");
					redirect(base_url().'admin/manager/edit_settings');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/manager/edit_settings');
			}
		}
		$data['pagePath']='Admin/Manager/edit_settings';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	private function uploadImage($files) {
		$config['upload_path'] = './assets/website/original';
		$config['allowed_types'] = '*';
		$config['remove_spaces'] = true;
		$config['file_name'] = time() . '-' . preg_replace('/[^A-Za-z0-9\-.]/', '',$files['conf_website_logo']['name']);
		$image_name = $config['file_name'];
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('conf_website_logo')) {
			return false;
		} else {
			$path=$this->upload->data();
			$configResize['image_library'] = 'gd2';
			$configResize['source_image'] = $path['full_path'];
			$configResize['new_image'] = './assets/website/thumb';
			$configResize['maintain_ratio'] = true;
			$configResize['width'] = 120;
			$configResize['height'] = 90;
			$this->load->library('image_lib',$configResize);
			if(!$this->image_lib->resize()){
				return false;
			} else {
				return $image_name;
			}
		}
	}
	
	function email_templates() {
		$data['templates'] = $this->manager_model->findAllEmailTemplates();
		$data['pagePath'] = 'Admin/Manager/email_templates';
		$data['pageJSPath']='Admin/Elements/js/datatable';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	function edit_email_template($template_id) {
		if(strlen($template_id) < 10){
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/manager/email_templates');
		}
		$template_id= convert_uudecode(base64_decode($template_id));
		$template = $this->manager_model->getEmailTemplateById($template_id);
		if(!isset($template->etpl_id)) {
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/manager/email_templates');
		}
		if($this->input->post()){
			$this->form_validation->set_rules('etpl_subject', 'Subject', 'trim|required');
			$this->form_validation->set_rules('etpl_body', 'Boady', 'trim|required');
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$update = array(
							'etpl_subject' => $post['etpl_subject'],
							'etpl_body' => $post['etpl_body'],
							'etpl_status' => $post['etpl_status']
						);
				if($this->manager_model->updateEmailTemplate($template_id,$update)){
					$this->session->set_flashdata('success',"Email Template has been updated successfuly.");
					redirect(base_url().'admin/manager/email_templates');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/manager/edit_email_template');
			}
		}
		$data['template'] = $template;
		$data['pagePath'] = 'Admin/Manager/edit_email_template';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
		function edit_mobile_pages() {
		if($this->input->post()){
			$this->form_validation->set_rules('privacy_policy', 'Privacy Policy Page', 'trim|required');
			$this->form_validation->set_rules('terms_condition', 'Terms and Conditions', 'trim|required');
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$this->manager_model->updateMobilePage('privacy_policy',$post['privacy_policy']);
				$this->manager_model->updateMobilePage('terms_condition',$post['terms_condition']);
				$this->session->set_flashdata('success',"Pages has been updated successfuly.");
				redirect(base_url().'admin/manager/edit_mobile_pages');
			}
		}
		$data['terms_condition'] = $this->manager_model->findMobilePage('terms_condition');
		$data['privacy_policy'] = $this->manager_model->findMobilePage('privacy_policy');
		$data['pagePath'] = 'Admin/Manager/edit_mobile_pages';
		$this->load->view('Admin/Layouts/admin',$data);
	}
}
