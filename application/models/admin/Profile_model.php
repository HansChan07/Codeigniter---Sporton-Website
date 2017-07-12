<?php
class Profile_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
	 
	function findAdminById($admin_id) {
		$admin_id = intval($admin_id);
		if($admin_id < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_admin', array('admin_id' =>$admin_id));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function updateAdmin($admin_id,$update) {
		$admin_id = intval($admin_id);
		if(intval($admin_id) < 0 || empty($update)){
				return false;
		}
		$this->db->where('admin_id', $admin_id);
		if($this->db->update('tbl_admin', $update)){
			return true;
		}
		return false;
	}
	
	function findStats() {
		$sql ="select count(user_id) as total_users from tbl_users";
		$query =  $this->db->query($sql);
		$users = $query->first_row();
		$data['totalUsers'] = $users->total_users;
		
		$sql ="select count(event_id) as total_events from tbl_events";
		$query =  $this->db->query($sql);
		$events = $query->first_row();
		$data['totalEvents'] = $events->total_events;
		return $data;
	}
}
