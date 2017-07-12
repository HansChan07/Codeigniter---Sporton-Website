<?php
class Mobile_model extends CI_Model {

     public function __construct() {
		parent::__construct();
     }
     
	 function isValidLogin($uemail,$upassword) {
		$password = md5($upassword);
		$this->db->where("(user_email = '$uemail' AND user_password = '$password')");
		$this->db->or_where("(user_login_name = '$uemail' AND user_password = '$password')");
		$query = $this->db->get('tbl_users');
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function isSamePassword($uid,$upassword) {
		$condition = array(
			'user_id' =>$uid,
			'user_password' =>md5($upassword)
		);
		$query = $this->db->get_where('tbl_users',$condition);
		if($query->num_rows() > 0){
			return true;
		} 
		return false;
	}
	
	function saveDevice($row){
		//remove all token for previous date
		$this->db->delete('tbl_devices', array('device_token' =>$row['device_token']));
		$this->db->insert('tbl_devices',$row);
		return true;
	}
	
	function deleteDevice($token){
		//remove all token for previous date
		$this->db->delete('tbl_devices', array('device_token' =>$token));
		return true;
	}
	
	function removeFriend($user_id,$friend_id){
		$this->db->where("(friend_from_request = $user_id AND friend_to_request = $friend_id)");
		$this->db->or_where("(friend_from_request = $friend_id AND friend_to_request = $user_id)");
		//$this->db->delete('tbl_friends', array('friend_from_request' =>$user_id,'friend_to_request'=>$friend_id));
		//$this->db->delete('tbl_friends', array('friend_from_request' =>$friend_id,'friend_to_request'=>$user_id));
		$this->db->delete('tbl_friends');
		return true;
	}
	
	function getUserById($user_id) {
		$user_id = intval($user_id);
		if(intval($user_id) < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_users', array('user_id' =>$user_id));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function getUserByEmail($email) {
		$query = $this->db->get_where('tbl_users', array('user_email' =>$email));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
    
    function getUserByEmailForFacebook($email) {
		$query = $this->db->get_where('tbl_users', array('user_email' =>$email, 'user_social_login' =>1));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
    
    function getUserByEmailAndToken($email,$token) {
		$query = $this->db->get_where('tbl_users', array('user_email' =>$email,'user_token' =>$token));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
    
    function getUserActiveStatus($user_id) {
        $user_id = intval($user_id);
		if(intval($user_id) < 0){
			return false;
		}
        $this->db->select('user_active_status');
        $query = $this->db->get_where('tbl_users', array('user_id' =>$user_id));
		if($query->num_rows() > 0){
			return $query->first_row();
		}
        return false;
    }

	function isUniqueEditEmail($user_id,$email) {
		$user_id = intval($user_id);
		if(intval($user_id) < 0 || empty($email)){
			return false;
		}
		$query = $this->db->get_where('tbl_users', array('user_id !='=>$user_id,'user_email' =>$email));
		if($query->num_rows() > 0){
			return false;
		} 
		return true;
	}
	
	function isUniqueRegisterEmail($email) {
		if(empty($email)){
			return false;
		}
		$query = $this->db->get_where('tbl_users', array('user_email' =>$email));
		if($query->num_rows() > 0){
			return false;
		} 
		return true;
	}
    
    function isUniqueRegisterEmailForSocial($email) {
		if(empty($email)){
			return false;
		}
		$query = $this->db->get_where('tbl_users', array('user_email' =>$email, 'user_social_login'=>1));
		if($query->num_rows() > 0){
			return false;
		} 
		return true;
	}
	
	function isUniqueUsername($username) {
		if(empty($username)){
			return false;
		}
		$query = $this->db->get_where('tbl_users', array('user_login_name' =>$username));
		if($query->num_rows() > 0){
			return false;
		} 
		return true;
	}
	function registerUser($insert) {
		if(empty($insert)){
			return false;
		}
		if($this->db->insert('tbl_users',$insert)){
			return $this->db->insert_id();
		}
		return false;
	}
	
	function updateUser($user_id,$update) {
		$user_id = intval($user_id);
		if(intval($user_id) < 0 || empty($update)){
			return false;
		}
		$this->db->where('user_id', $user_id);
		if($this->db->update('tbl_users', $update)){
			return true;
		}
		return false;
	}
	
	function getAllCategories($active = 0){
		$active = intval($active);
		$this->db->select('category_id,category_name,category_icon');
		$this->db->order_by("category_id", "asc"); 
		if($active == 1) {
			$query = $this->db->get_where('tbl_sports_category', array('category_status'=>1));
		} else {
			$query = $this->db->get('tbl_sports_category');
		}
		return $query->result_array();
	}
	
	function getSportsCategories($user_sports){
		$this->db->select('category_id,category_name,category_icon');
		$this->db->order_by("category_name", "asc"); 
		if(!empty($user_sports)) {
			$categories = json_decode($user_sports);
			$this->db->where_in('category_id',$categories);
			$query = $this->db->get_where('tbl_sports_category', array('category_status'=>1));
		} else {
			$query = $this->db->get_where('tbl_sports_category', array('category_status'=>1));
		}
		return $query->result_array();
	}
	
	function addEvent($event) {
		if(empty($event)){
			return false;
		}
		if($this->db->insert('tbl_events',$event)){
			return $this->db->insert_id();
		}
		return false;
	}
    
    // 2016-01-23, Upwork add new function to update event.
    function updateEvent($event_id,$update) {
		$event_id = intval($event_id);
		if(intval($event_id) < 0 || empty($update)){
			return false;
		}
		$this->db->where('event_id', $event_id);
		if($this->db->update('tbl_events', $update)){
			return true;
		}
		return false;
	}
	
	function getEventById($event_id) {
		$event_id = intval($event_id);
		if($event_id < 0){
			return false;
		}
		$this->db->select(array(
							'event_id',
                            'event_user_id',
                            'event_sports_category_id',
							'event_title',
							'event_description',
							"event_image",
							'event_location',
							'category_name',
							'category_icon',
                            "event_start_date as event_start_date_original",
                            "event_end_date as event_end_date_original",
                            "event_rating_date as event_rating_date_original",
							"DATE_FORMAT(event_start_date,'%W, %M, %D, %l:%i %p-') as event_start_date",
							"DATE_FORMAT(event_end_date,'%l:%i %p') as event_end_date",
							'user_name',
							"IF(user_image != '',user_image,'default_pic.jpeg') as user_image",
							'event_privacy',
							'event_gender',
							'event_minimum_skills',
							'event_players',
							'event_price',
							'event_latitude',
							'event_longitude',
							'event_user_id',
							'event_sports_category_id',
                            'event_rating_date'
						)
					);
		$this->db->join('tbl_sports_category','event_sports_category_id = category_id','left');
		$this->db->join('tbl_users','event_user_id = user_id','left');
		$query = $this->db->get_where('tbl_events', array('event_id' =>$event_id,'event_status'=>1));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function getAllEvents($user_id = 0,$event_type,$request_time) {
		$user_id = intval($user_id);
        $request_time_date = (!empty($request_time)?$request_time:date('Y-m-d H:i:s'));
		$conditions = array('event_status'=> 1);
		if($event_type == 2) {
			$conditions = array_merge($conditions,array('event_user_id'=>$user_id,'event_start_date >'=> $request_time_date));
		} else if($event_type == 3) {
            $conditions = array_merge($conditions,array('event_start_date >'=> $request_time_date));
			$event_ids = $this->getUserSaveEventsId($user_id);
			if(empty($event_ids)) {
				return array();
			}
			$this->db->where_in('event_id',$event_ids);			
		} else if ($event_type == 4) {
            $conditions = array_merge($conditions,array('event_start_date >'=> $request_time_date));
            /*Only retrieve the attanding events by this event_type*/
            $event_ids = $this->getUserAttandingEventsId($user_id);
            if(empty($event_ids)) {
				return array();
			}
			$this->db->where_in('event_id',$event_ids);	
        } else if ($event_type == 5) {
            // Query for archived events list.
            $conditions = array_merge($conditions,array('event_start_date <'=> $request_time_date));
            /*Only retrieve the attanding events by this event_type*/
            $event_ids = $this->getUserOwnedAndAttendedEventsId($user_id);
            if(!empty($event_ids)) {
                $this->db->where_in('event_id', $event_ids);
            }
        } else {
            $conditions = array_merge($conditions,array('event_start_date >'=> $request_time_date));
			$not_attanding_event_ids = $this->getUserNotAttandingEventsId($user_id);
			if(!empty($not_attanding_event_ids)) {
				$this->db->where_not_in('event_id',$not_attanding_event_ids);
			}
		}
		$this->db->select(array('event_id','event_user_id','event_title','event_description','event_image','event_location','category_name','category_icon',"DATE_FORMAT(event_start_date,'%d <br><b> %b <\b>') as start_date","DATE_FORMAT(event_start_date,'%h:%i %p') as start_time","DATE_FORMAT(event_end_date,'%b %d') as end_date","DATE_FORMAT(event_end_date,'%h:%i %p') as end_time",'event_start_date','event_end_date',"event_latitude","event_longitude",'event_sports_category_id'));
		$this->db->join('tbl_sports_category','event_sports_category_id = category_id','left');
        if ($event_type == 5) {
            $this->db->order_by('event_start_date', 'desc');
        } else {
            $this->db->order_by('event_start_date', 'asc');
        }
		$query = $this->db->get_where('tbl_events',$conditions);
		if($query->num_rows() > 0){
			return $query->result_array();
		} 
		return array();
	}
    
    function getUserOwnedAndAttendedEventsId($user_id) {
        $owned_and_attanding_event_ids = array();
		$user_id = intval($user_id);
		if($user_id < 0){
			return false;
		}
        $event_ids = $this->getUserAttandingEventsId($user_id);
        if(!empty($event_ids)) {
            $this->db->where_in('event_id', $event_ids);
        }
        $this->db->or_where('event_user_id', $user_id);
        
		$query = $this->db->get_where('tbl_events',array('event_status'=> 1));
		$results = $query->result_array();
		foreach($results as $result) {
			$owned_and_attanding_event_ids[] = $result['event_id'];
		}
		return array_unique($owned_and_attanding_event_ids);
    }
	
	function getUserNotAttandingEventsId($user_id) {
		$not_attanding_event_ids = array();
		$user_id = intval($user_id);
		if($user_id < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_event_to_users',array('evnt_user_id'=>$user_id,'evnt_type'=>3));
		$results = $query->result_array();
		foreach($results as $result) {
			$not_attanding_event_ids[] = $result['evnt_event_id'];
		}
		return array_unique($not_attanding_event_ids);
	}
    
    /* Add this function for get events only for attending items. */
    function getUserAttandingEventsId($user_id) {
		$attanding_event_ids = array();
		$user_id = intval($user_id);
		if($user_id < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_event_to_users',array('evnt_user_id'=>$user_id,'evnt_type'=>1));
		$results = $query->result_array();
		foreach($results as $result) {
			$attanding_event_ids[] = $result['evnt_event_id'];
		}
		return array_unique($attanding_event_ids);
	}
	
	function getUserSaveEventsId($user_id) {
		$event_ids = array();
		$user_id = intval($user_id);
		if($user_id < 0){
			return false;
		}
		$query = $this->db->get_where('tbl_save_events',array('save_events_user_id'=>$user_id));
		$results = $query->result_array();
		foreach($results as $result) {
			$event_ids[] = $result['save_events_event_id'];
		}
		return array_unique($event_ids);
	}

	function getEventUserStatus($user_id,$event_id) {
		$query = $this->db->get_where('tbl_event_to_users',array('evnt_event_id'=>$event_id,'evnt_user_id'=>$user_id));
		if($query->num_rows() > 0){
			$row = $query->first_row();
			if($row->evnt_type == 1) {
				return 'attending';
			} else if($row->evnt_type == 2) {
				return 'maybe';
			} else {
				return 'notattending';
			}
		} 
		return 'pending';
	}
	
	function saveUserEvent($event) {
		if(empty($event)){
			return false;
		}
		if($this->db->insert('tbl_save_events',$event)){
			return true;
		}
		return false;
	}

	function getPageBySlug($page_slug) {
		$query = $this->db->get_where('tbl_mobile_pages', array('page_slug' =>$page_slug));
		if($query->num_rows() > 0){
			return $query->first_row();
		} 
		return false;
	}
	
	function getRandomUsers($user_id,$limit = 10){
		$limit = intval($limit);
		$users_friend_ids = $this->findUsersfriendIds($user_id);
		if(!empty($users_friend_ids)) {
			$ignore = array_merge($users_friend_ids,array($user_id));
		} else {
			$ignore = $user_id;
		}
		$this->db->select(array('user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$this->db->where_not_in('user_id',$ignore);
		$this->db->limit($limit);
		$this->db->order_by('user_id', 'RANDOM');
		$query = $this->db->get_where('tbl_users', array('user_status'=>1));
		return $query->result_array();
	}
	
	function findUsersFriendIds($user_id,$request_status = 0){
		$friend_ids = array();
		$user_id = intval($user_id);
		if($user_id < 0){
			return false;
		}
		$this->db->where("(friend_from_request = $user_id OR friend_to_request = $user_id)");
		if(intval($request_status) !=0) {
			$query = $this->db->get_where('tbl_friends',array('friend_request_status'=>$request_status));
		} else {
			$query = $this->db->get('tbl_friends');
		}
		$results = $query->result_array();
		foreach($results as $result) {
			$friend_ids[] = $result['friend_from_request'];
			$friend_ids[] = $result['friend_to_request'];
		}
		return array_diff(array_unique($friend_ids),array($user_id));
	}
	
	function getAllFriends($user_id = 0){
		$user_id = intval($user_id);
		if($user_id < 0){
			return array();
		}
		$users_friend_ids = $this->findUsersFriendIds($user_id,1);
		if(empty($users_friend_ids)){
			return array();
		}
		$this->db->select(array('user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image",'user_sex'));
		$this->db->where_in('user_id',$users_friend_ids);
		$this->db->order_by('user_name', 'asc');
		$query = $this->db->get_where('tbl_users', array('user_status'=>1));
		return $query->result_array();
	}
	
	function findAllUserDevice($user_id = 0){
		$device_ids = array();
		$this->db->select('device_token');
		$query = $this->db->get_where('tbl_devices', array('device_user_id'=>$user_id));
		$tokens =  $query->result_array();
		foreach($tokens as $token) {
			$device_ids[] = $token['device_token'];
		}
		return $device_ids;
	}
	
	function isAlreadyFriends($user_id,$friend_id){
		$user_id = intval($user_id);
		$friend_id = intval($friend_id);
		if($user_id <= 0 || $friend_id <= 0){
			return false;
		}
		$this->db->where("(friend_from_request = $user_id AND friend_to_request = $friend_id)");
		$this->db->or_where("(friend_from_request = $friend_id AND friend_to_request = $user_id)");
		$query = $this->db->get('tbl_friends');
		if($query->num_rows() > 0) {
			return true;
		}
		return false;
	}
	
	function getFriendRequestActive($user_id,$friend_id){
		$this->db->where("(friend_from_request = $user_id AND friend_to_request = $friend_id)");
		$this->db->or_where("(friend_from_request = $friend_id AND friend_to_request = $user_id)");
		$query = $this->db->get('tbl_friends');
		if($query->num_rows() > 0) {
			$result = $query->first_row();
			if($result->friend_request_status == 1) {
				return 'no';
			} else if($result->friend_request_status == 0) {
				return 'pending';
			}			
		}
		return 'yes';
	}
	
	function saveFriendRequest($insert){
		if(empty($insert)){
			return false;
		}
		if($this->db->insert('tbl_friends',$insert)){
			return true;
		}
		return false;
	}
	
	function getPendingRequestsToUser($user_id = 0){
		$user_id = intval($user_id);
		if($user_id <= 0){
			return array();
		}
		$this->db->join('tbl_users','friend_from_request = user_id','left');
		$this->db->select(array('user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$query = $this->db->get_where('tbl_friends',array('friend_to_request'=>$user_id,'friend_request_status'=>0));
		return $query->result_array();
	}
	
	function getPendingRequestsByUser($user_id = 0){
		$user_id = intval($user_id);
		if($user_id <= 0){
			return array();
		}
		$this->db->join('tbl_users','friend_from_request = user_id','left');
		$this->db->select(array('user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$query = $this->db->get_where('tbl_friends',array('friend_from_request'=>$user_id,'friend_request_status'=>0));
		return $query->result_array();
	}
	
	function findFriendRequestId($user_id,$friend_id){
		$user_id = intval($user_id);
		$friend_id = intval($friend_id);
		if($user_id <= 0 || $friend_id <= 0){
			return false;
		}
		$conditions = array(
						'friend_from_request'=> $friend_id,
						'friend_to_request'=> $user_id,
						'friend_request_status'=>0
					);
		$query = $this->db->get_where('tbl_friends',$conditions);
		if($query->num_rows() > 0) {
			$result = $query->first_row();
			return $result->friend_id;
		}
		return false;
	}
	
	function updateFriendRequest($request_id,$update) {
		$request_id = intval($request_id);
		if(intval($request_id) < 0 || empty($update)){
			return false;
		}
		if($update != 2) {
			$this->db->where('friend_id', $request_id);
			if($this->db->update('tbl_friends', $update)){
				return true;
			}
		} else if($this->db->delete('tbl_friends', array('friend_id', $request_id))) {
				return true;
		}
		return false;
	}
	
	function getEventUsers($event_id,$type = 1) {
		$this->db->join('tbl_users','evnt_user_id = user_id','left');
		$this->db->select(array('user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$query = $this->db->get_where('tbl_event_to_users',array('evnt_event_id'=>$event_id,'evnt_type'=>$type));
		return $query->result_array();
	}
	
	function saveEventUser($insert,$type){
		if(empty($insert)){
			return false;
		}
		if($type == 'insert') {
			if($this->db->insert('tbl_event_to_users',$insert)){
				return true;
			}
		} else {
			if($this->db->update('tbl_event_to_users',array('evnt_type'=>$insert['evnt_type']),array('evnt_event_id'=>$insert['evnt_event_id'],'evnt_user_id'=>$insert['evnt_user_id']))){
				return true;
			}
		}
		return false;
	}
	
	function saveEventMessage($insert){
		if(empty($insert)){
			return false;
		}
		if($this->db->insert('tbl_event_message',$insert)){
			return true;
		}
		return false;
	}
	
	function getEventMessage($event_id,$limit = 25){
		$response = array();
		$limit = intval($limit);
		$event_id = intval($event_id);
		if($event_id <= 0) {
			return $response;
		}
		$this->db->select(array('event_message_description as message',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image",'event_message_create_user_time'));
		$this->db->join('tbl_users','event_message_user_id = user_id','left');
		$this->db->limit($limit);
		$this->db->order_by('event_message_date_added', 'DESC');
		$query = $this->db->get_where('tbl_event_message', array('event_message_event_id'=>$event_id));
		if($query->num_rows() > 0) {
			$results = $query->result_array();
			krsort($results);
			foreach($results as $result) {
				$response[] = $result;
			}
		}
		return $response;
	}
	
	function getVisitorFriends($user_id,$visitor_id){
		$users_ids = array_merge($this->findUsersFriendIds($user_id,1),array($user_id));
		$friends_ids = '('.implode(',',$users_ids).')';
		$pending_ids = $this->findUsersFriendIds($user_id,0);
		$pending_ids = '('.implode(',',(!empty($pending_ids)?$pending_ids:array(0))).')';
		$visitor_friend_ids = $this->findUsersFriendIds($visitor_id,1);
		if(empty($visitor_friend_ids)) {
			return array();
		}
		$this->db->select(array(
								'user_id',
								"IF(user_name != '',user_name,user_email) as user_name",
								"IF(user_image != '',user_image,'default_pic.jpeg') as user_image",
								"CASE
										WHEN user_id IN $friends_ids THEN 'no'
										WHEN user_id IN $pending_ids THEN 'pending'
										ELSE 'yes'
									END
								 as request_active"
							)
						);
		$this->db->where_in('user_id',$visitor_friend_ids);
		$this->db->order_by('user_name', 'asc');
		$query = $this->db->get_where('tbl_users', array('user_status'=>1));
		return $query->result_array();
	}
	
	function getSports($ids){
		if(empty($ids)) {
			return array();
		}
		$ids = json_decode($ids);
		$this->db->select(array('category_name as selected_sports','category_icon'));
		$this->db->where_in('category_id',$ids);
		$this->db->order_by('category_name', 'asc');
		$query = $this->db->get_where('tbl_sports_category', array('category_status'=>1));
		return $query->result_array();
	}
	
	function getSportsArray($ids){
		$sports  = array();
		if(empty($ids)) {
			return $sports;
		}
		$ids = json_decode($ids);
		foreach($ids as $id) {
			$sports[]['id'] = $id;
		}
		return $sports;
	}
		
	function getEventImages($user_id) {
		$this->db->select(array('event_image'));
		$query = $this->db->get_where('tbl_events',array('event_user_id'=>$user_id,'event_image !='=>''));
		if($query->num_rows() > 0){
			return $query->result_array();
		} 
		return array();
	}
	
	function findEventsForNotification() {
		$date = date('Y-m-d H:i:s');
		$conditions = array(
						'event_start_date >='=> date('Y-m-d H:i:s',strtotime("$date - 2 hours")),
						'event_start_date <='=>$date,
						'event_status'=> 1
					);
		$this->db->select(array('event_id','event_title'));
		$query = $this->db->get_where('tbl_events',$conditions);
		return $query->result();
	}
	
	function findUsersForNotification($event_id,$evnt_type = 2) {
		$this->db->select(array('evnt_user_id'));
		$query = $this->db->get_where('tbl_event_to_users',array('evnt_event_id'=>$event_id,'evnt_type'=>$evnt_type));
		return $query->result();
	}
	
	function getUserNotificationCount($user_id, $request_time) {
		$count = 0;
		if($user_id <= 0) {
			return $count;
		}
		$this->db->select(array('COUNT(notification_id) as total_notification'));
        // 2016-01-23, Upwork
		$query = $this->db->get_where('tbl_user_notification',array('notification_user_id'=>$user_id,'notification_read_status'=>0,'notification_date_effective  <'=> $request_time));
		if($query->num_rows() > 0){
			$result = $query->first_row();
			$count = $result->total_notification;
		} 
		return $count;
	}
	
	function getUserNotifications($user_id, $request_time) {
		$notifications = array();
		$this->db->select(array('user_name','notification_type','notification_message','notification_type_id','notification_send_by_id','notification_read_status ',"IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$this->db->join('tbl_users','tbl_users.user_id=tbl_user_notification.notification_send_by_id','left');
		$this->db->order_by("notification_date_added", "desc");
		$this->db->limit(20);		
		$query = $this->db->get_where('tbl_user_notification',array('notification_user_id'=>$user_id,'notification_date_effective  <'=> $request_time));
		if($query->num_rows() > 0){
			$this->db->get_where('tbl_user_notification',array('notification_user_id'=>$user_id,'notification_read_status'=>0,'notification_date_effective  <'=> $request_time));
			$notifications = $query->result_array();
			//update notification read status
			$update['notification_read_status'] = 1;
			$this->db->where(array('notification_user_id'=>$user_id,'notification_read_status'=>0, 'notification_date_effective  <'=> $request_time));
			$this->db->update('tbl_user_notification',$update);
		} 
		return $notifications;
	}
	
	function saveUserNotification($insert){
		if(empty($insert)){
			return false;
		}
		if($this->db->insert('tbl_user_notification',$insert)){
			return true;
		}
		return false;
	}
    /*2016-01-23, Upwork add this for rating notificioans*/
    function isAlreadySendRatingNotificationWithThisEvent($user_id,$event_id){
		$user_id = intval($user_id);
		$event_id = intval($event_id);
		if($user_id <= 0 || $event_id <= 0){
			return false;
		}
		$this->db->where("(notification_user_id = $user_id AND notification_type_id = $event_id AND notification_type = 'event_rating')");
		$query = $this->db->get('tbl_user_notification');
		if($query->num_rows() > 0) {
			return true;
		}
		return false;
	}
	
	function getEventSaveStatus($event_id,$user_id) {
		if($user_id <= 0) {
			return 0;
		}
		$query = $this->db->get_where('tbl_save_events',array('save_events_user_id'=>$user_id,'save_events_event_id'=>$event_id));
		if($query->num_rows() > 0){
			return 1;
		} 
		return 0;
	}
	
	function getFriendRequestCount($user_id) {
		$count = 0;
		if($user_id <= 0) {
			return $count;
		}
		$this->db->select(array('COUNT(friend_id) as total_request'));
		$query = $this->db->get_where('tbl_friends',array('friend_to_request'=>$user_id,'friend_request_status'=>0));
		if($query->num_rows() > 0){
			$result = $query->first_row();
			$count = $result->total_request;
		} 
		return $count;
	}
	
    function deleteUserEventAndUserSaveEvent($user_id,$event_id){
		$delete1 = $this->db->delete('tbl_save_events', array('save_events_user_id' =>$user_id,'save_events_event_id'=>$event_id));
        $delete2 = $this->db->delete('tbl_events', array('event_user_id' =>$user_id,'event_id'=>$event_id));
        
        if($delete1 || $delete2) {
			return true;
		}
		return false;
	}
    
	function deleteUserEvent($user_id,$event_id){
		if($this->db->delete('tbl_save_events', array('save_events_user_id' =>$user_id,'save_events_event_id'=>$event_id))) {
			return true;
		}
		return false;
	}
	
	/* functions for user rating module */
	
	function getUserAvgRating($user_id) {
		$this->db->select(array("AVG((skill_rate + sportmanship_rate + reliability_rate)/3) as rating"));
		$query = $this->db->get_where('tbl_user_rating', array('rating_for_user_id'=>$user_id));
		if($query->num_rows() > 0) {
			$data =  $query->first_row();
			return $data->rating;
		}
		return '';
	}
	
	function saveUserRating($insert,$action_type){
		if(empty($insert)){
			return false;
		}
		if($action_type == 0) {
			if($this->db->insert('tbl_user_rating',$insert)){
				return true;
			}
		} else {
			if($this->db->update('tbl_user_rating',array('skill_rate'=>$insert['skill_rate'],'sportmanship_rate'=>$insert['sportmanship_rate'],'reliability_rate'=>$insert['reliability_rate'],'rating_comment'=>$insert['rating_comment']),array('rating_for_user_id'=>$insert['rating_for_user_id'],'rating_by_user_id'=>$insert['rating_by_user_id']))){
				return true;
			}
		}
		return false;
	}
	
	function getAllRatings($user_id) {
        $ratings = array();
		$this->db->select(array('skill_rate','sportmanship_rate','reliability_rate','rating_comment','rating_id','rating_by_user_id as user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image", 'rating_added'));
		$this->db->join('tbl_users','tbl_users.user_id=tbl_user_rating.rating_by_user_id','left');
		$this->db->order_by("rating_added", "desc");
		$this->db->limit(20);
		$query = $this->db->get_where('tbl_user_rating',array('rating_for_user_id'=>$user_id));
		$ratings = $query->result_array();
		return $ratings;
	}
	
	function getRatingDetail($for_user_id,$by_user_id) {
		$rating = array();
		$this->db->select(array('skill_rate','sportmanship_rate','reliability_rate','rating_comment','rating_id','rating_by_user_id as user_id',"IF(user_name != '',user_name,user_email) as user_name","IF(user_image != '',user_image,'default_pic.jpeg') as user_image"));
		$this->db->join('tbl_users','tbl_users.user_id=tbl_user_rating.rating_by_user_id','left');
		$this->db->order_by("rating_added", "desc");
		$query = $this->db->get_where('tbl_user_rating',array('rating_for_user_id'=>$for_user_id,'rating_by_user_id'=>$by_user_id));
		$notifications = $query->result_array();
		if($query->num_rows() > 0) {
			$data =  $query->first_row();
			$rating['rating_id'] = $data->rating_id;
			$rating['skill_rate'] = $data->skill_rate;
			$rating['sportmanship_rate'] = $data->sportmanship_rate;
			$rating['reliability_rate'] = $data->reliability_rate;
			$rating['rating_comment'] = $data->rating_comment;
			$rating['user_id'] = $data->user_id;
			$rating['user_name'] = $data->user_name;
			$rating['user_image'] = $data->user_image;
			return $rating;
		}
		return false;
	}
	
	function findCompleteEvents() {
		$date = date('Y-m-d H:i:s');
		$conditions = array(
						'event_end_date <='=>$date,
						'event_status'=> 1
					);
		$this->db->select(array('event_id','event_title'));
		$query = $this->db->get_where('tbl_events',$conditions);
		$results = $query->result();
		if(!empty($results)) {
			$this->db->update('tbl_events',array('event_status'=>2),$conditions);
		}
		return $results;
	}
	
	function deleteCompletedEvents($token){
		$date = date('Y-m-d H:i:s');
		$conditions = array(
						'event_end_date >='=>date('Y-m-d H:i:s',strtotime("$date - 3 hours")),
						'event_status'=> 2
					);
		$this->db->delete('tbl_events',$conditions);
		return true;
	}
	
}
