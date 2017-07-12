<?php
class Profile extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('admin/profile_model');
		$logged_in = $this->session->userdata('logged_in');
		if(!$this->session->has_userdata('admin_id') || $logged_in == false) {
			redirect(base_url().'admin/auth/login');
		}
	}
	
	function dashboard(){
		$data['stats'] = $this->profile_model->findStats();
		$data['pagePath'] = 'Admin/Profile/dashboard';
		$this->load->view('Admin/Layouts/admin',$data);
	}

	function logout() {
		$this->load->model('admin/auth_model');
		//delete saved coooke info in table
		if(isset($_COOKIE['RMPASS'])) {
			$cookieData = $this->auth_model->findCookeUser($_COOKIE['RMPASS']);
			if(!empty($cookieData)) {
				$this->auth_model->deleteCookeData($cookieData->rmpassword_id);
			}
		}
		//unset session variable
		$this->session->unset_userdata('admin_id');
		$this->session->unset_userdata('admin_name');
		$this->session->unset_userdata('admin_email');
		$this->session->unset_userdata('admin_image');
		$this->session->unset_userdata('logged_in');
		$this->session->set_flashdata('success',"Logout successfully.");
		redirect(base_url().'admin/auth/login');
	}
	
	function edit_profile() {
		$admin_id = $this->session->userdata('admin_id');
		if($this->input->post()){
			$this->form_validation->set_rules('admin_mobile', 'Admin Mobile', 'trim|required|numeric|min_length[10]');
			$this->form_validation->set_rules('admin_name', 'Admin Name', 'trim|required');	
			$this->form_validation->set_rules('admin_email', 'Admin Email', 'trim|required|valid_email');		
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				if(!empty($_FILES['admin_image']['name']) && $_FILES['admin_image']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES);
					if(empty($image_name)) {
						$this->session->set_flashdata('error','Error in image uploading! Please try again.');
						redirect(base_url().'admin/profile/edit_profile');
					}
					$old_image = $this->session->userdata('admin_image');
					unlink('./uploads/admin/original/'.$old_image);
					unlink('./uploads/admin/thumb/'.$old_image);
				}
				$old_email = $this->session->userdata('admin_email');
				$update = array(
							'admin_name'  => $post['admin_name'],
							'admin_email' => $post['admin_email'],
							'admin_mobile' => $post['admin_mobile'],
							'admin_image' => ((!empty($image_name))?$image_name:$this->session->userdata('admin_image')),
						);
				if($this->profile_model->updateAdmin($admin_id,$update)){
					sendEmail($old_email,'profile_update',array("{username}"=>$post['admin_name']));
					$this->session->set_userdata($update);
					$this->session->set_flashdata('success',"Profile has been updated successfuly.");
					redirect(base_url().'admin/profile/dashboard');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/profile/edit_profile');
			}
		}
		$data['adminData'] = $this->profile_model->findAdminById($admin_id);
		$data['pagePath']='Admin/Profile/edit_profile';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	private function uploadImage($files) {
		$config['upload_path'] = './uploads/admin/original';
		$config['allowed_types'] = '*';
		$config['remove_spaces'] = true;
		$config['file_name'] = time() . '-' . preg_replace('/[^A-Za-z0-9\-.]/', '',$files['admin_image']['name']);
		$image_name = $config['file_name'];
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('admin_image')) {
			return false;
		} else {
			$path=$this->upload->data();
			$configResize['image_library'] = 'gd2';
			$configResize['source_image'] = $path['full_path'];
			$configResize['new_image'] = './uploads/admin/thumb';
			$configResize['maintain_ratio'] = false;
			$configResize['width'] = 255;
			$configResize['height'] = 255;
			$this->load->library('image_lib',$configResize);
			if(!$this->image_lib->resize()){
				return false;
			} else {
				return $image_name;
			}
		}
	}
	
	function change_password() {
		$admin_id = $this->session->userdata('admin_id');
		if($this->input->post()){
			$this->form_validation->set_rules('upassword', 'Old Password', 'trim|required');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');		
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$adminData = $this->profile_model->findAdminById($admin_id);
				if($adminData->admin_password != md5($post['upassword'])){
					$this->session->set_flashdata('error','You entered incorrect current password.');
					redirect(base_url().'admin/profile/change_password');
				}
				$update = array(
							'admin_password'  => md5($post['new_password'])
						);
				if($this->profile_model->updateAdmin($admin_id,$update)){
					sendEmail($adminData->admin_email,'change_password',array("{username}"=>$adminData->admin_name));
					$this->session->set_flashdata('success',"Password has been updated successfuly.");
					redirect(base_url().'admin/profile/dashboard');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/profile/change_password');
			}
		}
		$data['pagePath']='Admin/Profile/change_password';
		$this->load->view('Admin/Layouts/admin',$data);
	}
}
