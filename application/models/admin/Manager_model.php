<?php
class Manager_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
	
	function updateConfig($values) {
		if(empty($values)){
			return false;
		}
		foreach($values as $key => $val) {
			$this->db->update('tbl_configurations',array('conf_value'=>$val),array('conf_name'=>$key));
		}
		return true;
	}
	
	function findAllEmailTemplates() {
		$query = $this->db->get('tbl_email_templates');
		return $query->result();
	}
	
	function updateEmailTemplate($template_id,$update) {
		$template_id = intval($template_id);
		if(intval($template_id) < 0 || empty($update)){
			return false;
		}
		$this->db->where('etpl_id', $template_id);
		if($this->db->update('tbl_email_templates', $update)){
			return true;
		}
		return false;
	}
	
	function getEmailTemplateById($template_id) {
		$template_id = intval($template_id);
		if(intval($template_id) < 0){
			return false;
		}
		$condition = array('etpl_id'=>$template_id);
		$query = $this->db->get_where('tbl_email_templates',$condition);
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function findMobilePage($page_slug) {
		$query = $this->db->get_where('tbl_mobile_pages',array('page_slug'=>$page_slug));
		if($query->num_rows() > 0){
			$result = $query->first_row();
			return $result->page_description;
		}
		return false;
	}
	
	function updateMobilePage($page_slug,$description) {
		$this->db->update('tbl_mobile_pages',array('page_description'=>$description),array('page_slug'=>$page_slug));
	}
}

