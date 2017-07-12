<?php
class Event_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
	
	function findAllCategories() {
		$this->db->order_by('category_name','asc');
		$query = $this->db->get('tbl_sports_category');
		return $query->result();
	}
	
	function addUpdateCategory($cat_id,$update) {
		$cat_id = intval($cat_id);
		if(empty($update)){
			return false;
		}
		if($cat_id != 0) {
			$this->db->where('category_id', $cat_id);
			if($this->db->update('tbl_sports_category', $update)){
				return true;
			}
		} else if($this->db->insert('tbl_sports_category',$update)){
				return true;
		}
		return false;
	}
	
	function getCategoryById($cat_id) {
		$cat_id = intval($cat_id);
		if(intval($cat_id) < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_sports_category',array('category_id'=>$cat_id));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function findAllEvents() {
		$this->db->select(array('event_id','event_title','event_location','event_status',"IF(user_name != '',user_name,user_email) as user_name",'category_name',"DATE_FORMAT(event_start_date,'%d %b %h:%i %p') as start_date","DATE_FORMAT(event_end_date,'%d %b %h:%i %p') as end_date"));
		$this->db->join('tbl_sports_category','event_sports_category_id = category_id','left');
		$this->db->join('tbl_users','event_user_id = user_id','left');
		$this->db->order_by('event_date_added','desc');
		$query = $this->db->get('tbl_events');
		return $query->result();
	}
	
	function addUpdateEvent($event_id,$update) {
		$event_id = intval($event_id);
		if(empty($update)){
			return false;
		}
		if($event_id != 0) {
			$this->db->where('category_id', $event_id);
			if($this->db->update('tbl_events', $update)){
				return true;
			}
		} else if($this->db->insert('tbl_events',$update)){
				return true;
		}
		return false;
	}
	
	function getEventById($event_id) {
		$event_id = intval($event_id);
		if($event_id < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_events', array('event_id' =>$event_id));
		if($query->num_rows() > 0){
			return $query->result_array();
		} 
		return false;
	}
	
	function getDropDwonCategory($active = 0) {
		$this->select('category_id,category_name');
		$query = $this->db->get('tbl_sports_category');
		$this->db->order_by("category_name", "asc"); 
		if($active==1) {
			$this->db->where('category_status',1);
		}
		return $query->result();
	}
	
	function deleteEvent($event_id) {
		$event_id = intval($event_id);
		if($event_id < 0){
			return false;
		}
		if($this->db->delete('tbl_events', array('event_id' =>$event_id))) {
			return true;
		}
		return false;
	}
}

