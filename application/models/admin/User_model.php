<?php
class User_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
	
	function findAllUsers() {
		$this->db->order_by('user_added','desc');
		$query = $this->db->get('tbl_users');
		return $query->result();
	}
}

