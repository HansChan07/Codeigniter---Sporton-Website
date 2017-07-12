<?php
class User extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('admin/user_model');
		$logged_in = $this->session->userdata('logged_in');
		if(!$this->session->has_userdata('admin_id') || $logged_in == false) {
			redirect(base_url().'admin/auth/login');
		}
	}
	
	function users() {
		$data['users'] = $this->user_model->findAllUsers();
		$data['pagePath'] = 'Admin/User/users';
		$data['pageJSPath']='Admin/Elements/js/datatable';
		$this->load->view('Admin/Layouts/admin',$data);
	}
}
