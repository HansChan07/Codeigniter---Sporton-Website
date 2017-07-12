<?php
class Event extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('admin/event_model');
		$logged_in = $this->session->userdata('logged_in');
		if(!$this->session->has_userdata('admin_id') || $logged_in == false) {
			redirect(base_url().'admin/auth/login');
		}
	}
	
	function categories() {
		$data['categories'] = $this->event_model->findAllCategories();
		$data['pagePath'] = 'Admin/Event/categories';
		$data['pageJSPath']='Admin/Elements/js/datatable';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	function add_category() {
		if($this->input->post()){
			$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
			$this->form_validation->set_rules('category_status', 'Category Status', 'trim|required');
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$update = array(
							'category_name' => $post['category_name'],
							'category_status' => intval($post['category_status'])
						);
				if($this->event_model->addUpdateCategory(0,$update)){
					$this->session->set_flashdata('success',"Category has been added successfuly.");
					redirect(base_url().'admin/event/categories');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/event/categories');
			}
		}
		$data['pagePath'] = 'Admin/Event/add_category';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	function edit_category($cat_id) {
		if(strlen($cat_id) < 10){
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/event/categories');
		}
		$cat_id= convert_uudecode(base64_decode($cat_id));
		$category = $this->event_model->getCategoryById($cat_id);
		if(!isset($category->category_id)) {
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/event/categories');
		}
		if($this->input->post()){
			$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
			$this->form_validation->set_rules('category_status', 'Category Status', 'trim|required');
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				$post = $this->input->post();
				$update = array(
							'category_name' => $post['category_name'],
							'category_status' => intval($post['category_status'])
						);
				if($this->event_model->addUpdateCategory($cat_id,$update)){
					$this->session->set_flashdata('success',"Category has been updated successfuly.");
					redirect(base_url().'admin/event/categories');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/event/categories');
			}
		}
		$data['category'] = $category;
		$data['pagePath'] = 'Admin/Event/edit_category';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	function events() {
		$data['events'] = $this->event_model->findAllEvents();
		$data['pagePath'] = 'Admin/Event/events';
		$data['pageJSPath']='Admin/Elements/js/datatable';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	function delete_event($event_id) {
		if(strlen($event_id) < 10){
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/event/events');
		}
		$event_id= convert_uudecode(base64_decode($event_id));
		if(!$this->event_model->deleteEvent($event_id)) {
			$this->session->set_flashdata('error','Error occur! Please try again.');
			redirect(base_url().'admin/event/events');
		}
		$this->session->set_flashdata('success','Event deleted successfully.');
		redirect(base_url().'admin/event/events');
	}
	
	function edit_event($event_id) {
		if(strlen($event_id) < 10){
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/event/events');
		}
		$cat_id= convert_uudecode(base64_decode($cat_id));
		$category = $this->event_model->getCategoryById($cat_id);
		if(!isset($category->category_id)) {
			$this->session->set_flashdata('error','Invalid Link! Please try again.');
			redirect(base_url().'admin/event/events');
		}
		if($this->input->post()){
			$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
			$this->form_validation->set_rules('category_status', 'Category Status', 'trim|required');
			$this->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
			$this->form_validation->set_rules('category_status', 'Category Status', 'trim|required');
			$this->form_validation->set_error_delimiters(
					'<div class="alert alert-danger alert-dismissable">
						<i class="fa fa-ban"></i>
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#10006</button>
						<b>Error!</b>','</div>');
			if($this->form_validation->run()){
				if(!empty($_FILES['page_image']['name']) && $_FILES['page_image']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES,200,350);
					if(empty($image_name)) {
						$this->session->set_flashdata('error','Error in image uploading! Please try again.');
						redirect(base_url().'admin/event/edit_event/'.base64_encode(convert_uuencode($page_id)));
					}
				}
				$post = $this->input->post();
				$event = array(
							'event_sports_category_id' => intval($post['category_id']),
							'event_title' => $post['event_title'],
							'event_description' => $post['event_description'],
							'event_image' => $image_name,
							'event_location' => $post['event_location'],
							//'event_start_date' => $post['event_start_date'],
							//'event_end_date' => $post['event_end_date'],
							'event_privacy' => $post['event_privacy'],
							'event_gender' => intval($post['event_gender']),
							'event_minimum_skills' => $post['event_minimum_skills'],
							'event_min_players' => intval($post['event_min_players']),
							'event_max_players' => intval($post['event_max_players']),
							'event_price' => $post['event_price']						
						);
				if($this->event_model->addUpdateCategory($cat_id,$update)){
					$this->session->set_flashdata('success',"Category has been updated successfuly.");
					redirect(base_url().'admin/event/categories');
				}
				$this->session->set_flashdata('error','Error occur! Please try again.');
				redirect(base_url().'admin/event/categories');
			}
		}
		$data['category'] = $category;
		$data['pagePath'] = 'Admin/Event/edit_category';
		$this->load->view('Admin/Layouts/admin',$data);
	}
	
	private function uploadImage($files, $h = 118, $w = 148) {
		$config['upload_path'] = './uploads/mobile/original';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['remove_spaces'] = true;
		$config['file_name'] = time() . '-' . preg_replace('/[^A-Za-z0-9\-.]/', '',$files['page_image']['name']);
		$image_name = $config['file_name'];
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('page_image')) {
			return false;
		} else {
			$path=$this->upload->data();
			$configResize['image_library'] = 'gd2';
			$configResize['source_image'] = $path['full_path'];
			$configResize['new_image'] = './uploads/mobile/thumb';
			$configResize['maintain_ratio'] = false;
			$configResize['width'] = $w;
			$configResize['height'] = $h;
			$this->load->library('image_lib',$configResize);
			if(!$this->image_lib->resize()){
				return false;
			} else {
				return $image_name;
			}
		}
	}
}
