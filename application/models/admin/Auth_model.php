<?php
class Auth_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
     
	 function isValidLogin($uemail,$upassword) {
		$condition = array(
			'admin_email' =>$uemail,
			'admin_password' =>md5($upassword)
		);
		$query = $this->db->get_where('tbl_admin',$condition);
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function getUniqueToken() {
		$token = getRandomPassword(25);
		$query = $this->db->get_where('tbl_rmpassword', array('rmpassword_token' =>md5($token)));
		if($query->num_rows() <= 0){
			return $token;
		} else {
			$this->getUniqueToken();
		}
	}
	
	function saveRemeberMe($row) {
		$this->db->insert('tbl_rmpassword',$row);
		return true;
	}
	
	function findCookeUser($token) {
		//remove all token for previous date
		$this->db->delete('tbl_rmpassword', array('rmpassword_expire <' => date('Y-m-d H:i:s')));
		$query = $this->db->get_where('tbl_rmpassword', array('rmpassword_token' =>md5($token)));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function getAdminByEmail($email) {
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
			return false;
		}
		$query = $this->db->get_where('tbl_admin', array('admin_email' =>$email));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function deleteCookeData($rmpassword_id) {
		$rmpassword_id = intval($rmpassword_id);
		if($rmpassword_id < 0) {
			return false;
		}
		//remove token for this browser
		$this->db->delete('tbl_rmpassword', array('rmpassword_id' =>$rmpassword_id));
		return false;
	}
	
	function getAdminByToken($token) {
		$query = $this->db->get_where('tbl_admin', array('admin_token' =>md5($token),'admin_token_expire > '=> date('Y-m-d H:i:s')));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}

}
