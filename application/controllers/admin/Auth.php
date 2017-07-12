<?php
class Auth extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('admin/auth_model');
		$logged_in = $this->session->userdata('logged_in');
		if($this->session->has_userdata('admin_id') || $logged_in == true) {
			redirect(base_url().'admin/profile/dashboard');
		}
	}
	
	function login() {
		$this->checkRemeberMe();
		if($this->input->post()){
			$this->form_validation->set_rules('uemail', 'User Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('upassword', 'User Password', 'trim|required');			
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$adminData  = $this->auth_model->isValidLogin($post['uemail'],$post['upassword']);
				if(!empty($adminData) && $adminData->admin_password===md5($post['upassword'])) {
					if(isset($post['remember_me']) && $post['remember_me']=='on') {
						$this->saveRemeberMe($adminData->admin_id,1);
					}
					$this->createSession($adminData);
					redirect(base_url().'admin/profile/dashboard');
				} else {
					$this->session->set_flashdata('error',"Invalid username or password.Please try again.");
				}
			}
		}
		$data['pagePath']='Admin/Auth/login';
		$this->load->view('Admin/Layouts/login',$data);
	}
	
	private function createSession($row){
		if(empty($row)) {
			return false;
		}
		$admin = array(
				'admin_id' => $row->admin_id,
                'admin_name'  => $row->admin_name,
                'admin_email' => $row->admin_email,
				'admin_image' => ((!empty($row->admin_image))?$row->admin_image:'nopic.jpg'),
                'logged_in' => TRUE
          );
		$this->session->set_userdata($admin);
		return true;
	}
	
	private function checkRemeberMe() {
		if(isset($_COOKIE['RMPASS'])) {
			$cookieData = $this->auth_model->findCookeUser($_COOKIE['RMPASS']);
			if(!empty($cookieData)) {
				$this->load->model('admin/profile_model');
				$userData = $this->profile_model->findAdminById($cookieData->rmpassword_user_id);
				if(!empty($userData)){
					$this->createSession($userData);
					redirect(base_url().'admin/profile/dashboard');
				}
			}
		}
	}
	
	private function saveRemeberMe($user_id,$user_type) {
		$user_id = intval($user_id);
		$user_type = intval($user_type);
		if($user_id < 0 || $user_type < 0) {
			return;
		}
		$token = $this->auth_model->getUniqueToken();
		setcookie('RMPASS',$token, time() + (86400 * 30 * 7), "/"); // 7 day
		$expire = date('Y-m-d h:i:s',strtotime('+7 days'));
		$ip = (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'');
		$browser = (isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'');
		$rmdata = array(
				'rmpassword_user_id'=> $user_id,
				'rmpassword_user_type' => $user_type,
				'rmpassword_token'=> md5($token),
				'rmpassword_browser'=> $browser,
				'rmpassword_ip' => $ip,
				'rmpassword_expire'=> $expire
			);
		$this->auth_model->saveRemeberMe($rmdata);
		return true;
	}
	
	function forgotPassword() { 
		if($this->input->post()){
			$this->form_validation->set_rules('uemail', 'User Email', 'trim|required|valid_email');	
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$adminData = $this->auth_model->getAdminByEmail($post['uemail']);
				if(!empty($adminData) && $adminData->admin_email===$post['uemail']) {
					if($adminData->admin_token_expire > date('Y-m-d H:i:s')) {
						$this->session->set_flashdata('error','Your request to reset password has already been placed within last 30 minutes. Please check your email or retry after 30 minutes of your previous request.');
						redirect(base_url().'admin/auth/login');
					}
					$token = getRandomPassword(25);
					$forgotdata = array(
							'admin_token'=> md5($token),
							'admin_token_expire'=> date('Y-m-d H:i:s', strtotime("+30 minutes"))
						); 
					$this->load->model('admin/profile_model');
					if($this->profile_model->updateAdmin($adminData->admin_id,$forgotdata)) {
						$reset_url = base_url()."admin/auth/resetPassword/".$token;
						sendEmail($adminData->admin_email,'forgot_password',array("{reset_url}"=>$reset_url,"{username}"=>$adminData->admin_name));
						$this->session->set_flashdata('success','Your password instructions has been sent to your email. Please check your email.');
						redirect(base_url().'admin/auth/login');
					}
					$this->session->set_flashdata('error','Error occur! Please try again.');
					redirect(base_url().'admin/auth/login');
				}
				$this->session->set_flashdata('error','This email address does not exist in our database.');
				redirect(base_url().'admin/auth/login');
			}
		}
		$data['pagePath']='Admin/Auth/forgotPassword';
		$this->load->view('Admin/Layouts/login',$data);
	}
		
	function resetPassword($activation_key) {
		$adminData = $this->auth_model->getAdminByToken($activation_key);
		if($this->input->post() && !empty($adminData)) {
			$this->form_validation->set_rules('upassword', 'New Password', 'trim|required');
			$this->form_validation->set_rules('confpassword', 'Confirm Password', 'trim|required|matches[upassword]');			
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$token = getRandomPassword(25);
				$passworddata = array(
							'admin_password'=> md5($post['upassword']),
							'admin_token'=> md5($token),
							'admin_token_expire'=> date('Y-m-d H:i:s')
						);
				$this->load->model('admin/profile_model');
				if($this->profile_model->updateAdmin($adminData->admin_id,$passworddata)) {
					sendEmail($adminData->admin_email,'reset_password',array("{username}"=>$adminData->admin_name));
					$this->session->set_flashdata('success',"Password changed successfully. Please login here.");
					redirect(base_url().'admin/auth/login');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/auth/login');
			}
			$this->session->set_flashdata('formError',validation_errors());
			redirect(base_url().'admin/auth/resetPassword/'.$activation_key);
		} else if(!$this->input->post() && !empty($adminData)){
			$data['pagePath'] = 'Admin/Auth/resetPassword';
			$data['activation_key'] = $activation_key;
			$this->load->view('Admin/Layouts/login',$data);
		} else {
			$this->session->set_flashdata('error',"Expired link! Please try with valid link.");
			redirect(base_url().'admin/auth/login');
		}
	}
}
