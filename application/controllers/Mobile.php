<?php
class Mobile extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('mobile_model');
	}
	
	function login() {
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->isValidLogin(strtolower($post['user_email']),$post['user_password']);
			if(!empty($userData) && $userData->user_password===md5($post['user_password'])) {
				//save user device id for future use
				$update = array(
							'device_user_id'=>$userData->user_id,
							'device_token'=>$post['device_id']
						);
				$this->mobile_model->saveDevice($update);
				
				$response['responseCode'] = 1;
				$response['user'] = $this->userForResponse($userData->user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid Username or Password';
				die(json_encode($response));
			}
		}
	}
	
	function logout() {
		if($this->input->post()){
			$post = $this->input->post();
			if($this->mobile_model->deleteDevice($post['device_id'])) {
				$response['responseCode'] = 1;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again';
				die(json_encode($response));
			}
		}
	}
	
	function forgot_password() {
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserByEmail(strtolower($post['user_email']));
			if(!empty($userData)){
				$password = getRandomPassword(8);
				$update = array('user_password'=>md5($password));
				if($this->mobile_model->updateUser($userData->user_id,$update)) {
					sendEmail($userData->user_email,'mobile_forgot_password',array("{password}"=>$password,"{username}"=>$userData->user_name));
					$response['responseCode'] = 1;
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Email address does not exist.';
				die(json_encode($response));
			}
		}
	}
	
	function change_password(){
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
				//for same user password
				if(!$this->mobile_model->isSamePassword($userData->user_id,$post['user_old_password'])) {
					$response['responseCode'] = 0;
					$response['message'] = 'Old password does not match.';
					die(json_encode($response));
				}
				$update = array('user_password'=> md5($post['user_new_password']));
				if($this->mobile_model->updateUser($userData->user_id,$update)) {
					$response['responseCode'] = 1;
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid User details.';
				die(json_encode($response));
			}
		}
	}
	
	function update_user(){
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
 				if(!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES);
					if(empty($image_name)) {
						$response['responseCode'] = 0;
						$response['message'] = 'Error in image uploading. Please try again';
						die(json_encode($response));
					}
				}
				$user_name = (!empty($post['user_name'])?$post['user_name']:$userData->user_name);
				$update = array(
							'user_name'=> $user_name,
							'user_dob' => (!empty($post['user_dob'])?$post['user_dob']:$userData->user_dob),
							'user_about' => (!empty($post['user_about'])?$post['user_about']:$userData->user_about),
							'user_sex' => (!empty($post['user_sex'])?intval($post['user_sex']):$userData->user_sex),
							'user_location' => (!empty($post['user_location'])?$post['user_location']:$userData->user_location),
							'user_sports' => (!empty($post['user_sports'])?json_encode(explode(",",trim($post['user_sports']))):$userData->user_sports),
							'user_image' => (!empty($image_name)?$image_name:$userData->user_image),
						);
				if($this->mobile_model->updateUser($userData->user_id,$update)) {
					//send request to suggested user
					if(!empty($post['suggested_users'])) {
						$msg = $user_name.' has send friend request.';
						$message = array(
											'key'=> 'new_friend_request',
											'message' => $msg,
										);
						$users = explode(',',$post['suggested_users']);
                        // Add the user local phone's time for the future distinish.
                        $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
						foreach($users as $user) {
							if(!$this->mobile_model->isAlreadyFriends($userData->user_id,$user)) {
								$request = array(
										'friend_from_request'=>$userData->user_id,
										'friend_to_request'=>$user
									);
								if($this->mobile_model->saveFriendRequest($request)) {
                                    $this->send_push_notification($user,$message);
								}
							}
						}
					}
					//delete old image save
					if(!empty($image_name) && !empty($userData->user_image)) {
						unlink('./uploads/mobile/original/'.$userData->user_image);
						unlink('./uploads/mobile/thumb/'.$userData->user_image);
					}
					$response['responseCode'] = 1;
					$response['user'] = $this->userForResponse($userData->user_id);
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid User details.';
				die(json_encode($response));
			}
		}
	}
	
	function user_settings(){
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
				$update = array('user_notification'=> intval($post['user_notification']));
				if($this->mobile_model->updateUser($userData->user_id,$update)) {
					$response['responseCode'] = 1;
					$response['status'] = (intval($post['user_notification'])==1?'Active':'Deactive');
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid User details.';
				die(json_encode($response));
			}
		}
	}
	
	function register() {
		if($this->input->post()){
			$post = $this->input->post();
			if(!$this->mobile_model->isUniqueRegisterEmail(strtolower($post['user_email']))) {
				$response['responseCode'] = 0;
				$response['message'] = 'Email address already exist.';
				die(json_encode($response));
			}
			//if(!$this->mobile_model->isUniqueUsername(strtolower($post['user_login_name']))) {
			//	log_message('debug','kevin in isUniqueUsername');
			//	$response['responseCode'] = 0;
			//	$response['message'] = 'This user name already exist.';
			//	die(json_encode($response));
			//}
			log_message('debug','kevin befor insert = array');
            $token = md5($post['user_email']);
			$insert = array(
						'user_email'=>strtolower($post['user_email']),
						'user_login_name'=>strtolower($post['user_login_name']),
						'user_password'=>md5($post['user_password']),
                        'user_active_status'=>0,
                        'user_token'=>$token
						);
			if($user_id = $this->mobile_model->registerUser($insert)) {
				//save user device id for future use
				if(!empty($post['device_id'])) {
					$update = array(
								'device_user_id'=>$user_id,
								'device_token'=>$post['device_id']
							);
					$this->mobile_model->saveDevice($update);
				}
                // kevin send email to verification this account.
                $this->activate_email_account($post['user_email'],$token);
				$response['responseCode'] = 1;
				$response['user'] = $this->userForResponse($user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
    
    // 2016-01-23, Upwork add new function to regist facebook.
    function register_facebook() {
		if($this->input->post()){
			$post = $this->input->post();
			if(!$this->mobile_model->isUniqueRegisterEmailForSocial(strtolower($post['user_email']))) {
				$response['responseCode'] = 2;
                $userData  = $this->mobile_model->getUserByEmailForFacebook($post['user_email']);
                $response['user'] = $this->userForResponse($userData->user_id);
				$response['message'] = 'This facebook account address already been registered, directly login.';
				die(json_encode($response));
			}
			//if(!$this->mobile_model->isUniqueUsername(strtolower($post['user_login_name']))) {
			//	log_message('debug','kevin in isUniqueUsername');
			//	$response['responseCode'] = 0;
			//	$response['message'] = 'This user name already exist.';
			//	die(json_encode($response));
			//}
            
            if(!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0){  
				$image_name = $this->uploadImage($_FILES);
				if(empty($image_name)) {
				    $response['responseCode'] = 0;
				    $response['message'] = 'Error in image uploading. Please try again';
				    die(json_encode($response));
				}
            }
			$insert = array(
						'user_email'=>strtolower($post['user_email']),
                        'user_name'=>strtolower($post['user_login_name']),
						'user_login_name'=>strtolower($post['user_login_name']),
                        'user_dob'=>strtolower($post['user_dob']),
                        'user_location'=>strtolower($post['user_location']),
                        'user_sex'=>intval($post['user_gender']),
                        'user_fb_app_id'=>strtolower($post['user_facebook_id']),
                        'user_social_login'=>1,
                        'user_active_status'=>1,
                        'user_image' => (!empty($image_name)?$image_name:"")
						);
			if($user_id = $this->mobile_model->registerUser($insert)) {
				//save user device id for future use
				if(!empty($post['device_id'])) {
					$update = array(
								'device_user_id'=>$user_id,
								'device_token'=>$post['device_id']
							);
					$this->mobile_model->saveDevice($update);
				}
				$response['responseCode'] = 1;
				$response['user'] = $this->userForResponse($user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	private function userForResponse($user_id){
		$userData  = $this->mobile_model->getUserById($user_id);
		$user['user_id'] = $user_id;
		$user['user_name'] = (!empty($userData->user_name)?$userData->user_name:'');
		$user['user_image'] = (!empty($userData->user_image)?$userData->user_image:'default_pic.jpeg');
        $user['user_active_status'] = $userData->user_active_status;
        $user['user_sex'] = $userData->user_sex;
		return $user;
	}
	
	private function uploadImage($files, $h = 118, $w = 148) {
		$config['upload_path'] = './uploads/mobile/original';
		$config['allowed_types'] = '*';
		$config['remove_spaces'] = true;
		$config['file_name'] = time() . '-' . preg_replace('/[^A-Za-z0-9\-.]/', '',$files['image']['name']);
		$image_name = $config['file_name'];
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('image')) {
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
	
	function sports_categories() {
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
				$response['responseCode'] = 1;
				$response['categories'] = $this->mobile_model->getSportsCategories($userData->user_sports);
				$response['friends'] = $this->mobile_model->getAllFriends($userData->user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function create_event(){
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
				if(!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES,200,350);
					if(empty($image_name)) {
						$response['responseCode'] = 0;
						$response['message'] = 'Error in image uploading. Please try again';
						die(json_encode($response));
					}
				}
				if($location = $this->getCoordByAddress($post['event_location'])){
					$longitude = $location['lat'];
					$latitude = $location['long'];
				} else {
					$longitude = 0;
					$latitude = 0;
				}
				$event = array(
							'event_user_id'=> intval($post['user_id']),
							'event_sports_category_id' => intval($post['category_id']),
							'event_title' => $post['event_title'],
							'event_description' => $post['event_description'],
							'event_image' => !empty($image_name)?$image_name:'',
							'event_location' => $post['event_location'],
							'event_latitude' => $longitude,
							'event_longitude' => $latitude,
							'event_start_date' => $post['event_start_date'],
							'event_end_date' => $post['event_end_date'],
                            'event_rating_date' => $post['event_rating_date'],
							'event_privacy' => $post['event_privacy'],
							'event_gender' => intval($post['event_gender']),
							'event_minimum_skills' => $post['event_minimum_skills'],
							'event_players' => intval($post['event_players']),
							'event_price' => !empty($post['event_price'])?$post['event_price']:'Free',
						);
				if($event_id = $this->mobile_model->addEvent($event)) {
                    // Upwork add 2016-5-27, the caption name should be automatic added in participates list when event is creating.
			        $request_save_caption = array(
							'evnt_user_id'=>intval($post['user_id']),
							'evnt_event_id'=>$event_id,
							'evnt_type'=>1
						);
                    $this->mobile_model->saveEventUser($request_save_caption,"insert");
                    
					if(!empty($post['send_invite'])) {
						$msg = $userData->user_name .' has invited you to a new event.';
						$message = array(
											'key'=> 'event_invite',
											'event_id' => $event_id,
											'message' => $msg,
										);
						$users = explode(',',$post['send_invite']);
                        log_message('debug','kevin in mobile.php'.$post['send_invite']);
                        // kevin add the user local phone's time for the future distinish.
                        $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
						foreach($users as $user) {
							$notification = array(
												'notification_user_id'=>$user,
												'notification_type' => 'event_invite',
												'notification_type_id' => $event_id,
												'notification_message'=> $msg,
												'notification_send_by_id' => $userData->user_id,
                                                'notification_date_effective' => $request_time,
											);
							$this->mobile_model->saveUserNotification($notification);
							$this->send_push_notification($user,$message);
						}
					}
					$response['responseCode'] = 1;
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid User details.';
				die(json_encode($response));
			}
		}
	}
    
    // 2015-12-10 Add new function to update event.
    function update_event(){
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
            $eventData = $this->mobile_model->getEventById($post['event_id']);
			if(!empty($userData)){
				if(!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0){  
					$image_name = $this->uploadImage($_FILES,200,350);
					if(empty($image_name)) {
						$response['responseCode'] = 0;
						$response['message'] = 'Error in image uploading. Please try again';
						die(json_encode($response));
					}
				}
				if($location = $this->getCoordByAddress($post['event_location'])){
					$longitude = $location['lat'];
					$latitude = $location['long'];
				} else {
					$longitude = 0;
					$latitude = 0;
				}
				$event = array(
							'event_user_id'=> (!empty($post['user_id'])?intval($post['user_id']):$eventData->event_user_id),
							'event_sports_category_id' => (!empty($post['category_id'])?intval($post['category_id']):$eventData->event_sports_category_id),
							'event_title' => $post['event_title'],
							'event_description' => $post['event_description'],
							'event_image' => !empty($image_name)?$image_name:$eventData->event_image,
							'event_location' => $post['event_location'],
							'event_latitude' => $longitude,
							'event_longitude' => $latitude,
							'event_start_date' => $post['event_start_date'],
							'event_end_date' => $post['event_end_date'],
                            'event_rating_date' => $post['event_rating_date'],
							'event_privacy' => $post['event_privacy'],
							'event_gender' => intval($post['event_gender']),
							'event_minimum_skills' => $post['event_minimum_skills'],
							'event_players' => intval($post['event_players']),
							'event_price' => !empty($post['event_price'])?$post['event_price']:'Free',
						);
				if($event_id = $this->mobile_model->updateEvent($post['event_id'], $event)) {
					if(!empty($post['send_invite'])) {
						$msg = $userData->user_name .' has invite to new event.';
						$message = array(
											'key'=> 'event_invite',
											'event_id' => $event_id,
											'message' => $msg,
										);
						$users = explode(',',$post['send_invite']);
                        // kevin add the user local phone's time for the future distinish.
                        $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
						foreach($users as $user) {
							$notification = array(
												'notification_user_id'=>$user,
												'notification_type' => 'event_invite',
												'notification_type_id' => $event_id,
												'notification_message'=> $msg,
												'notification_send_by_id' => $userData->user_id,
                                                'notification_date_effective' => $request_time,
											);
							$this->mobile_model->saveUserNotification($notification);
							$this->send_push_notification($user,$message);
						}
					}
					$response['responseCode'] = 1;
					die(json_encode($response));
				}
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Invalid User details.';
				die(json_encode($response));
			}
		}
	}
	
	function get_event_detail() {
		if($this->input->post()){
			$post = $this->input->post();
			$event_id  = intval($post['event_id']);
			$user_id  = intval($post['user_id']);
			if($event_id <= 0) {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
			$eventData  = $this->mobile_model->getEventById($event_id);
			if(!empty($eventData)){
				$event['event_id'] = $eventData->event_id;
                $event['event_user_id'] = $eventData->event_user_id;
                $event['event_category_id'] = $eventData->event_sports_category_id;
				$event['event_title'] = $eventData->event_title;
				$event['event_description'] = $eventData->event_description;
				$event['user_image'] = $eventData->user_image;
				$event['event_location'] = $eventData->event_location;
                $event['event_start_date_original'] = $eventData->event_start_date_original;
                $event['event_end_date_original'] = $eventData->event_end_date_original;
                $event['event_rating_date_original'] = $eventData->event_rating_date_original;
				$event['event_start_date'] = $eventData->event_start_date;
				$event['event_end_date'] = $eventData->event_end_date;
				$event['category_name'] = $eventData->category_name;
				$event['category_icon'] = $eventData->category_icon;
				$event['captain_id'] = $eventData->event_user_id;
				$event['user_name'] = $eventData->user_name;
				if(!empty($eventData->event_image)) {
					$event['event_image'] = $eventData->event_image;
				} else {
					$event['event_image'] = $this->getEventImage($eventData->event_sports_category_id);
				} 
				$event['event_privacy'] = $eventData->event_privacy;
				$event['event_gender'] = $eventData->event_gender;
				$event['event_minimum_skills'] = $eventData->event_minimum_skills;
				$event['event_players'] = $eventData->event_players;
				$event['event_price'] = $eventData->event_price;
				$event['latitude'] = $eventData->event_latitude;
				$event['longitude'] = $eventData->event_longitude;
				$event['event_user_status'] = $this->mobile_model->getEventUserStatus($user_id,$event_id);
				if(!empty($post['user_latitude']) && !empty($post['user_longitude'])) {
					$event['distance'] = $this->getDistance($eventData->event_latitude,$eventData->event_longitude,$post['user_latitude'],$post['user_longitude']);
				}				
				$response['responseCode'] = 1;
				$response['event'] = array_merge($event,$this->getLocationInfo($eventData->event_location)); 
				$response['maybe'] = $this->mobile_model->getEventUsers($event_id,2);
				//dummy attending user
				$attending = $this->mobile_model->getEventUsers($event_id,1);
				$size = sizeof($attending);
				$limit = $eventData->event_players;
				if($size >= $limit) {
					$response['attending'] = $attending;
				} else {
					for($i = $size; $i <= $limit-1; $i++) {
						$dummyUser = array();
						$dummyUser['user_id'] = '';
						$dummyUser['user_name'] = 'User';
						$dummyUser['user_image'] = 'default_attending_pics.png';
						$attending[$i] = $dummyUser;
					}
					$response['attending'] = $attending;
				}
				$response['messages'] = $this->mobile_model->getEventMessage($event_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
    
    function cancel_user_event() { 
        if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$event_id = intval($post['event_id']);
            if($this->mobile_model->deleteUserEventAndUserSaveEvent($user_id,$event_id)) {
				$response['responseCode'] = 1;
				die(json_encode($response));
            } else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
            }
		}
    }
	
	function getEventImage($event_id) {
		switch($event_id) {
			case 16:
				$event_image = 'Hockey.jpg';
				break;
			case 17:
				$event_image = 'Golf.jpg';
				break;
			case 18:
				$event_image = 'Football.jpg';
				break;
			case 19:
				$event_image = 'Soccer.jpg';
				break;
			case 20:
				$event_image = 'Tennis.jpg';
				break;
			case 21:
				$event_image = 'Baseball.jpg';
				break;
			case 22:
				$event_image = 'Skiing.jpg';
				break;
			case 23:
				$event_image = 'Cricket.jpg';
				break;
			case 24:
				$event_image = 'Volleyball.jpg';
				break;
			case 25:
				$event_image = 'Basketball.jpg';
				break;
			case 26:
				$event_image = 'TableTennis.jpg';
				break;
			case 27:
				$event_image = 'Badmington.jpg';
				break;
			case 28:
				$event_image = 'Running.jpg';
				break;
			case 29:
				$event_image = 'Yoga.jpg';
				break;
			case 30:
				$event_image = 'Boxing.jpg';
				break;
			case 31:
				$event_image = 'MartialArts.jpg';
				break;
			case 32:
				$event_image = 'Cycling.jpg';
				break;
			case 33:
				$event_image = 'Rollerblade.jpg';
				break;
			case 34:
				$event_image = 'Cycling.jpg';
				break;
			case 35:
				$event_image = 'Watersports.jpg';
				break;
			case 36:
				$event_image = 'Climbing.jpg';
				break;
            case 37:
                $event_image = 'Yoga.jpg';
				break;
			default: 
				$event_image = 'default_event.jpg';
				break;
		}
		return $event_image;
	}	
	
	function get_page() {
		if($this->input->post()){
			$post = $this->input->post();
			$pageData  = $this->mobile_model->getPageBySlug($post['page_slug']);
			if(!empty($pageData)){
				$response['responseCode'] = 1;
				$page['page_description'] = $pageData->page_description;
				$response['page'] = $page;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function get_user() {
		if($this->input->post()){
			$post = $this->input->post();
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			if(!empty($userData)){
				$response['responseCode'] = 1;
				$user['user_id'] = $userData->user_id;
				$user['user_name'] = $userData->user_name;
				$user['user_sex'] = (!empty($userData->user_sex)?$userData->user_sex:'');
				$user['user_dob'] = ($userData->user_dob !='0000-00-00'?$userData->user_dob:'');
				$user['user_about'] = (!empty($userData->user_about)?$userData->user_about:'');
				$user['user_image'] = (!empty($userData->user_image)?$userData->user_image:'default_pic.jpeg');
				$user['user_location'] = (!empty($userData->user_location)?$userData->user_location:'');
				$user['user_sports'] = $this->mobile_model->getSportsArray($userData->user_sports);
				$response['user'] = $user;
				$response['categories'] = $this->mobile_model->getAllCategories(1);
				$response['suggested_users'] = $this->mobile_model->getRandomUsers($userData->user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function public_profile() {
		if($this->input->post()){
			$post = $this->input->post();
			$visitor_id = intval($post['visitor_id']);
			$user_id = intval($post['user_id']);
			$userData  = $this->mobile_model->getUserById($visitor_id);
			if(!empty($userData)){
				$response['responseCode'] = 1;
				$user['user_id'] = $userData->user_id;
				$user['user_name'] = $userData->user_name;
				$user['user_age'] = (!empty($userData->user_dob)?$this->getAge($userData->user_dob):'');
				$user['user_image'] = (!empty($userData->user_image)?$userData->user_image:'default_pic.jpeg');
				$user['user_location'] = (!empty($userData->user_location)?$userData->user_location:'');
				$user['user_about'] = (!empty($userData->user_about)?$userData->user_about:'');
				$user['user_sports'] = $this->mobile_model->getSports($userData->user_sports);
				$user['request_active'] = ($this->mobile_model->getFriendRequestActive($user_id,$visitor_id));
				$user['user_rating'] = floor($this->mobile_model->getUserAvgRating($visitor_id));
				$response['user'] = $user;
				$response['friends'] = $this->mobile_model->getVisitorFriends($user_id,$visitor_id);
				$events = $this->mobile_model->getAllEvents($visitor_id,2,"");
				$i = 0;
				foreach($events as $event) {
					if(!empty($event['event_image'])) {
						$events[$i]['event_image'] = $event['event_image'];
					} else {
						$events[$i]['event_image'] = $this->getEventImage($event['event_sports_category_id']);
					}
					$i++;
				}
				$response['events'] = $events;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function personal_profile() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$userData  = $this->mobile_model->getUserById($user_id);
			if(!empty($userData)){
				$response['responseCode'] = 1;
				$user['user_id'] = $userData->user_id;
				$user['user_name'] = $userData->user_name;
				$user['user_age'] = (!empty($userData->user_dob)?$this->getAge($userData->user_dob):'');
				$user['user_image'] = (!empty($userData->user_image)?$userData->user_image:'default_pic.jpeg');
				$user['user_location'] = (!empty($userData->user_location)?$userData->user_location:'');
				$user['user_about'] = (!empty($userData->user_about)?$userData->user_about:'');
				$user['user_sports'] = $this->mobile_model->getSports($userData->user_sports);
				$user['user_rating'] = floor($this->mobile_model->getUserAvgRating($user_id));
				$response['user'] = $user;
				$response['friends'] = $this->mobile_model->getAllFriends($user_id);
				$events = $this->mobile_model->getAllEvents($user_id,2,"");
				$i = 0;
				foreach($events as $event) {
					if(!empty($event['event_image'])) {
						$events[$i]['event_image'] = $event['event_image'];
					} else {
						$events[$i]['event_image'] = $this->getEventImage($event['event_sports_category_id']);
					}
					$i++;
				}
				$response['events'] = $events;
				$response['images'] = $this->mobile_model->getEventImages($user_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	private function getAge($date) {
		if($date == '0000-00-00') {
			return '';
		}
		$date_diff = strtotime(date('Y-m-d h:i:s'))-strtotime($date);
		return floor(($date_diff)/(60*60*24*365)) ." years old";
	}
	
	function user_friend_list() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$response['responseCode'] = 1;
			//$response['friends'] =$this->mobile_model->getAllFriends($user_id);
			//$response['pending_requests'] = $this->mobile_model->getPendingRequestsByUser($user_id);
			$response['friend_requests'] = $this->mobile_model->getPendingRequestsToUser($post['user_id']);
			die(json_encode($response));
		}
	}
	
	function get_events() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = (!empty($post['user_id'])?$post['user_id']:0);
			$eventsData  = $this->mobile_model->getAllEvents($user_id,$post['event_type'],$post['request_time']);
			if(!empty($eventsData) && !empty($post['user_latitude']) && !empty($post['user_longitude'])) {
				$i = 0;
				foreach($eventsData as $event) {
					$eventsData[$i]['distance'] = $this->getDistance($event['event_latitude'],$event['event_longitude'],$post['user_latitude'],$post['user_longitude']);
					$eventsData[$i]['user_save_status'] = $this->mobile_model->getEventSaveStatus($event['event_id'],$user_id);
					if(!empty($event['event_image'])) {
						$eventsData[$i]['event_image'] = $event['event_image'];
					} else {
						$eventsData[$i]['event_image'] = $this->getEventImage($event['event_sports_category_id']);
					}
					$i++;
				}
			}
			$response['responseCode'] = 1;
            // kevin add time check of count notification.
			$response['notification_count'] = $this->mobile_model->getUserNotificationCount($user_id,$post['request_time']);
			$response['request_count'] = $this->mobile_model->getFriendRequestCount($user_id);
			$response['event_type'] = intval($post['event_type']);
			$response['events'] = $eventsData;
            $response['categories'] = $this->mobile_model->getAllCategories(1);
            $response['friends'] = $this->mobile_model->getAllFriends($user_id);
            if ($user_id != 0) {
                $userActiveData = $this->mobile_model->getUserActiveStatus($user_id);
                $response['user_active_status'] = $userActiveData -> user_active_status;
            }
			die(json_encode($response));
		}
	}
	
	function save_user_event() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$event_id = intval($post['event_id']);
			$action_type = intval($post['action_type']);
			if($action_type == 0) {
				$request = array(
						'save_events_user_id'=>$user_id,
						'save_events_event_id'=>$event_id
					);
				if($this->mobile_model->saveUserEvent($request)) {
					$response['responseCode'] = 1;
					die(json_encode($response));
				} else {
					$response['responseCode'] = 0;
					$response['message'] = 'Something went wrong. Please try again.';
					die(json_encode($response));
				}
			} else if($action_type == 1) {
				if($this->mobile_model->deleteUserEvent($user_id,$event_id)) {
					$response['responseCode'] = 1;
					die(json_encode($response));
				} else {
					$response['responseCode'] = 0;
					$response['message'] = 'Something went wrong. Please try again.';
					die(json_encode($response));
				}
			} else {
					$response['responseCode'] = 0;
					$response['message'] = 'Something went wrong. Please try again.';
					die(json_encode($response));
			}
		}
	}
	
	function save_event_message() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$event_id = intval($post['event_id']);
            $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
			$request = array(
						'event_message_user_id'=>$user_id,
						'event_message_event_id'=>$event_id,
						'event_message_description'=>$post['message'],
                        'event_message_create_user_time'=>$request_time
					);
			if($this->mobile_model->saveEventMessage($request)) {
                $eventData = $this->mobile_model->getEventById($event_id);
                
				$userDetails = $this->mobile_model->getUserById($user_id);
				$user_name = $userDetails->user_name;
                $msg = $user_name.', posted a message to the event, '.$eventData->event_title.'.';
                $message = array(
                                    'key'=> 'event_message',
                                    'event_id' => $event_id,
                                    'message' => $msg,
                                );
                
                if ($user_id != $eventData->event_user_id) {
				    $notification = array(
                            'notification_user_id'=>$eventData->event_user_id,
				            'notification_type' => 'event_message',
				            'notification_type_id' => $event_id,
				            'notification_message'=> $msg,
				            'notification_send_by_id' => $user_id,
                            'notification_date_effective' => $request_time,
				        );
				    $this->mobile_model->saveUserNotification($notification);
                    $this->send_push_notification($eventData->event_user_id,$message);
                }
                    
                // 2016-01-23, Upwork loop notification each users.
                $attendingUsers = $this->mobile_model->getEventUsers($event_id,1);
                $size = sizeof($attendingUsers);
                    
                foreach($attendingUsers as $attendUser) {
                    if ($attendUser['user_id'] != $user_id) {
                        $notificationforMessage = array(
				            'notification_user_id'=>$attendUser['user_id'],
				            'notification_type' => 'event_message',
				            'notification_type_id' => $event_id,
				            'notification_message'=> $msg,
				            'notification_send_by_id' => $user_id,
                            'notification_date_effective' => $request_time,
				        );
                        $this->mobile_model->saveUserNotification($notificationforMessage);
                        $this->send_push_notification($attendUser['user_id'],$message);
                    }
                }
                /*2016-01-23 Upwork end*/
				$response['responseCode'] = 1;
				$response['messages'] = $this->mobile_model->getEventMessage($event_id);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function send_friend_request() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$friend_id = intval($post['friend_id']);
			if($this->mobile_model->isAlreadyFriends($user_id,$friend_id)) {
				$response['responseCode'] = 0;
				$response['message'] = 'Friend request pending.';
				die(json_encode($response));
			}
			$request = array(
						'friend_from_request'=>$user_id,
						'friend_to_request'=>$friend_id
					);
			if($this->mobile_model->saveFriendRequest($request)) {
                $msg = 'Friend request pending.';
				$message = array(
								'key'=> 'new_friend_request',
								'message' => $msg,
							);
                $this->send_push_notification($friend_id,$message);
				$response['responseCode'] = 1;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function remove_friend() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$friend_id = intval($post['friend_id']);
			if($this->mobile_model->removeFriend($user_id,$friend_id)) {
				$response['responseCode'] = 1;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function friend_requests() {
		if($this->input->post()){
			$post = $this->input->post();
			$requestData  = $this->mobile_model->getPendingFriendRequest($post['user_id']);
			if(!empty($requestData)){
				$response['responseCode'] = 1;
				$response['requests'] = $requestData;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'No pending friend request found.';
				die(json_encode($response));
			}
		}
	}
	
	function update_friend_request() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$friend_id = intval($post['friend_id']);
			if(!$request_id = $this->mobile_model->findFriendRequestId($user_id,$friend_id)) {
				$response['responseCode'] = 0;
				$response['message'] = 'Request not found.';
				die(json_encode($response));
			}
			$request = array('friend_request_status'=>intval($post['request_type']));
			if($this->mobile_model->updateFriendRequest($request_id,$request)) {
				if(intval($post['request_type'])==1) {
					$userDetails = $this->mobile_model->getUserById($user_id);
					$user_name = $userDetails->user_name;
					$msg = $user_name.' has accepted your friend request.';
					$message = array(
									'key'=> 'accept_friend_request',
									'message' => $msg,
								);
                    // 2016-01-23 Upwork add the user local phone's time for the future distinish.
                    $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
					$notification = array(
									'notification_user_id'=>$friend_id,
									'notification_type' => 'accept_friend_request',
									'notification_type_id' => $user_id,
									'notification_message'=> $msg,
									'notification_send_by_id' => $user_id,
                                    'notification_date_effective' => $request_time,
								);
				    $this->mobile_model->saveUserNotification($notification);
                    $this->send_push_notification($friend_id,$message);
				}
				$response['responseCode'] = 1;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function add_event_participation() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$event_id = intval($post['event_id']);
			$event_status = $this->mobile_model->getEventUserStatus($user_id,$event_id);
			$type = "insert";
			if($event_status != 'pending') {
				$type = "update";
			}
			$request = array(
							'evnt_user_id'=>$user_id,
							'evnt_event_id'=>$event_id,
							'evnt_type'=>intval($post['request_type'])
						);
            
            // 2016-01-25 Upwork prepare for notify all other already attended users.
            if (intval($post['request_type'])==1){
                $attendingUsers = $this->mobile_model->getEventUsers($event_id,1);
                $size = sizeof($attendingUsers);
            }
            
			if($this->mobile_model->saveEventUser($request,$type)) {
                /*2016-01-23, Upwork add notification here.*/
                if(intval($post['request_type'])==1 || intval($post['request_type'])==2) {
                    $eventData = $this->mobile_model->getEventById($event_id);
					$userDetails = $this->mobile_model->getUserById($user_id);
					$user_name = $userDetails->user_name;
                    $msg = 'GameON! '.$user_name.' is attending '.$eventData->event_title.'. Click to see details.';
                    if (intval($post['request_type'])==2){
                        $msg = $user_name.', maybe attending your event, '.$eventData->event_title.'.';
                    }
                    // Upwork add the user local phone's time for the future distinish.
                    $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
					$notification = array(
									'notification_user_id'=>$eventData->event_user_id,
									'notification_type' => 'event_attend',
									'notification_type_id' => $event_id,
									'notification_message'=> $msg,
									'notification_send_by_id' => $user_id,
                                    'notification_date_effective' => $request_time,
								);
				    $this->mobile_model->saveUserNotification($notification);
                    
                    $message = array(
                                    'key'=> 'participation_event_notification',
                                    'event_id' => $event_id,
                                    'message' => $msg,
                                );
                    $this->send_push_notification($eventData->event_user_id,$message);
                    // Notify all attending users, new user is attending.
                    if (intval($post['request_type'])==1){
                        foreach($attendingUsers as $attendUser) {
                            $notificationforattending = array(
									'notification_user_id'=>$attendUser['user_id'],
									'notification_type' => 'event_attend',
									'notification_type_id' => $event_id,
									'notification_message'=> $msg,
									'notification_send_by_id' => $user_id,
                                    'notification_date_effective' => $request_time,
								);
                            $this->mobile_model->saveUserNotification($notificationforattending);
                            
                            $messageloop = array(
                                    'key'=> 'participation_event_notification',
                                    'user_id' => $attendUser['user_id'],
                                    'message' => $msg,
                                );
                            $this->send_push_notification($attendUser['user_id'],$messageloop);
                        }
                    }
                }
                
                /*2016-01-25, Upwork If user attend this event, we prepare the schedule push rating notification*/
                if(intval($post['request_type'])==1) {
                    $eventDataForRating = $this->mobile_model->getEventById($event_id);
                    /*2016-01-25 Upwork, Check whether the rating notificioan has been sent. We only sent rating notification one time.*/
                    if(!$this->mobile_model->isAlreadySendRatingNotificationWithThisEvent($user_id,$event_id)) {
                    $msgnotification = 'Hi, Hope you had fun at, '.$eventDataForRating->event_title.'! Please contribute to the SportON community by leaving a review of your teammate. Thanks.';
                    $notificationRating = array(
									'notification_user_id'=> $user_id,
									'notification_type' => 'event_rating',
									'notification_type_id' => $event_id,
									'notification_message'=> $msgnotification,
									'notification_send_by_id' => $eventData->event_user_id,
                                    'notification_date_effective' => $eventData->event_rating_date,
								);
				    $this->mobile_model->saveUserNotification($notificationRating);
                    }
                }
                
                /*2016-01-25, Upwork end*/
                
				$response['responseCode'] = 1;
				$response['request_type'] = intval($post['request_type']);
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function get_notifications() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = (!empty($post['user_id'])?$post['user_id']:0);
            // 2016-01-26 Upwork, add the user local phone's time for the future distinish.
            $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
			$response['responseCode'] = 1;
            // 2016-01-26 Upwork, add time check.
			$response['notifications']= $this->mobile_model->getUserNotifications($user_id, $request_time);
			die(json_encode($response));
		}
	}
	
	function get_user_friends() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$response['responseCode'] = 1;
			$response['friends'] =$this->mobile_model->getAllFriends($user_id);
			die(json_encode($response));
		}
	}
	
	function share_event_with_friends() {
		if($this->input->post()){
			$post = $this->input->post();
			$event_id = intval($post['event_id']);
			$userData  = $this->mobile_model->getUserById($post['user_id']);
			$eventData = $this->mobile_model->getEventById($event_id);
			if(!empty($userData) && !empty($eventData)) {
				$msg = $userData->user_name .' has share '.$eventData->event_title.' event with you.';
				$message = array(
								'key'=> 'event_sharing',
								'event_id' => $event_id,
								'message' => $msg,
							);
				$users = explode(',',$post['friends_ids']);
                // 2016-01-25, Upwork add the user local phone's time for the future distinish.
                $request_time = (!empty($post['request_time'])?$post['request_time']:date('Y-m-d H:i:s'));
				foreach($users as $user) {
					$notification = array(
										'notification_user_id'=>$user,
										'notification_type' => 'event_sharing',
										'notification_type_id' => $event_id,
										'notification_message'=> $msg,
										'notification_send_by_id' => $userData->user_id,
                                        'notification_date_effective' => $request_time,
									);
					$this->mobile_model->saveUserNotification($notification);
					$this->send_push_notification($user,$message);
				}
				$response['responseCode'] = 1;
				die(json_encode($response));
			}
			$response['responseCode'] = 0;
			$response['message'] = 'Something went wrong. Please try again.';
			die(json_encode($response));
		}
	}
	
	private function getLocationInfo($address) {
		$result = array(
					'temperature'=>'',
					'forecast'=>''
				);		
		$address = preg_replace('/[^A-Za-z0-9]/',' ',$address);		
		$address = preg_replace('!\s+!', ' ', $address);
		$address = str_replace(' ','+',$address);
		$geocode = @file_get_contents("https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22".$address."%2C%20ak%22)&format=json");
		$output = json_decode($geocode);
        //log_message('debug','Upwork in mobile.php, and the weather result is:'.$output);
		if($output->query->count > 0) {
			if(!empty($output->query->results->channel->item->condition->temp)){
				$result['temperature'] = floor(($output->query->results->channel->item->condition->temp - 32) / 1.8);
			}
			if(!empty($output->query->results->channel->item->condition->text)){
				$result['forecast'] = $output->query->results->channel->item->condition->text;
			}
		}
		return $result;
	}
	
	function getCoordByAddress($address){
		$address = preg_replace('/[^A-Za-z0-9]/',' ',$address);		
		$address = preg_replace('!\s+!', ' ', $address);
		$address = str_replace(' ','+',$address);
		$geocode = @file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
		$output= json_decode($geocode);
		if($output->status=='OK') {
			$result['lat'] = $output->results[0]->geometry->location->lat;
			$result['long'] = $output->results[0]->geometry->location->lng;
			return $result;
		}
		return false;
	}
	
	function getDistance($lat1,$lon1,$lat2,$lon2) {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		return sprintf('%0.1f',($miles * 1.609344)).'km away';
	}
	
	function send_wating_notification1() {
		sendEmail('mramitkumar93@gmail.com','mobile_forgot_password',array("{password}"=>123,"{username}"=>'Amit'));
	}
	
	function send_wating_notification() {
	//sendEmail('mramitkumar93@gmail.com','mobile_forgot_password',array("{password}"=>123,"{username}"=>'Amit'));
		$events = $this->mobile_model->findEventsForNotification();
		if(!empty($events)) {
			foreach($events as  $event) {
				$users = $this->mobile_model->findUsersForNotification($event->event_id);
				if(!empty($user)) {
					$msg = $event->event_title.' will be start within 2 hours.';
					$message = array(
									'key'=> 'maybe_event_notification',
									'event_id' => $event->event_id,
									'message' => $msg,
								);
					foreach($users as $user){
						$notification = array(
											'notification_user_id'=>$user->evnt_user_id,
											'notification_type' => 'maybe_event_notification',
											'notification_type_id' => $event->event_id,
											'notification_message'=> $msg,
											'notification_send_by_id' => 0,
										);
						$this->mobile_model->saveUserNotification($notification);
						$this->send_push_notification($user->evnt_user_id,$message);
					}
				}
			}
		}
		//send review notification
		$events = $this->mobile_model->findCompleteEvents();
		if(!empty($events)) {
			foreach($events as  $event) {
				$users = $this->mobile_model->findUsersForNotification($event->event_id,1);
				if(!empty($user)) {
					$msg = $event->event_title.' completed. Please rate captain.';
					$message = array(
									'key'=> 'complete_event_notification',
									'user_id' => $event->event_user_id,
									'message' => $msg,
								);
					foreach($users as $user){
						$notification = array(
											'notification_user_id'=>$user->evnt_user_id,
											'notification_type' => 'complete_event_notification',
											'notification_type_id' => $event->event_user_id,
											'notification_message'=> $msg,
											'notification_send_by_id' => 0,
										);
						$this->mobile_model->saveUserNotification($notification);
						$this->send_push_notification($user->evnt_user_id,$message);
					}
				}
			}
		}
		
		//delete completed event after 3 hrs
		$this->mobile_model->deleteCompletedEvents();
	}
	
	function send_push_notification($user_id,$message) {
		$user_id = intval($user_id);
		$userData  = $this->mobile_model->getUserById($user_id);
		if(empty($userData)){
			return true;
		}
		if($userData->user_notification == 0) {
			return true;
		}
		$device_ids = $this->mobile_model->findAllUserDevice($user_id);
        
        log_message('debug','Upwork in send_push_notification $device_ids number is: '.count($device_ids).', the user id is:'.$user_id);
		if(!empty($device_ids)) {	
			//$url = 'https://android.googleapis.com/gcm/send';
            $url = 'https://gcm-http.googleapis.com/gcm/send';
			$fields = array(
					'registration_ids' => $device_ids,
					'data' => $message,
				);
            // 2016-01-25 Upwork API Key.
            $apiKey = 'AIzaSyCEQZjVbJiGgrvNIgUzN7bKQuwGUCfoMRs';
            // Old key.
			//define('GOOGLE_API_KEY','AIzaSyDwfRf1nzFc7PWYb7cCHREVZJ4RU6AsMxw');
			$headers = array(
					'Authorization: key='.$apiKey,
					'Content-Type: application/json'
				);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			// Error handling
            if ( curl_errno( $ch )) {
                log_message('debug','Upwork in send_push_notification GCM error: '.curl_error( $ch ));
            }

			curl_close($ch);
            // Debug GCM response
            log_message('debug','Upwork in send_push_notification GCM result is: '.$result);
		}
		return true;
	}
	
	/* functions for user rating module */
	
	function save_user_rating() {
		if($this->input->post()){
			$post = $this->input->post();
			$for_user_id = intval($post['for_user_id']);
			$by_user_id = intval($post['by_user_id']);
			$skill_rate = $post['skill_rate'];
			$sportmanship_rate = $post['sportmanship_rate'];
			$reliability_rate = $post['reliability_rate'];
			$comment = $post['comment'];
			$action_type = intval($post['action_type']);
			$request = array(
						'rating_for_user_id'=>$for_user_id,
						'rating_by_user_id'=>$by_user_id,
						'rating_comment'=>$comment,
						'skill_rate'=>$skill_rate,
						'sportmanship_rate'=>$sportmanship_rate,
						'reliability_rate'=>$reliability_rate
					);
			if($this->mobile_model->saveUserRating($request,$action_type)) {
				$response['responseCode'] = 1;
				die(json_encode($response));
			} else {
				$response['responseCode'] = 0;
				$response['message'] = 'Something went wrong. Please try again.';
				die(json_encode($response));
			}
		}
	}
	
	function get_rating_list() {
		if($this->input->post()){
			$post = $this->input->post();
			$user_id = intval($post['user_id']);
			$response['responseCode'] = 1;
			$response['ratings'] =$this->mobile_model->getAllRatings($user_id);
			die(json_encode($response));
		}
	}
	
	function get_rating_detail() {
		if($this->input->post()){
			$post = $this->input->post();
			$for_user_id = intval($post['for_user_id']);
			$by_user_id = intval($post['by_user_id']);
			if($rating = $this->mobile_model->getRatingDetail($for_user_id,$by_user_id)) {
				$response['responseCode'] = 1;
				$response['rating'] = $rating;
				$response['allratings'] = $this->mobile_model->getAllRatings($for_user_id);
			} else {
				$response['responseCode'] = 0;
				$response['rating'] = 'Not yet rated. Please rate.';
			}
			die(json_encode($response));
		}
	}
    
    // Upwork add at 2015-12-30 to send email to verify the user's account information:
    function activate_email_account($email,$verificationText) {
         $config=Array(
                'useragent' => "CodeIgniter",
                'mailpath' => "/usr/bin/sendmail",
                'crlf' => "\r\n",
                'newline' => "\r\n",
                'charset' => 'utf-8',
                'protocol' => 'smtp',  
                'smtp_host'=>  "smtp.mailgun.org",                // SMTP Server.  Example: mail.earthlink.net  
                'smtp_user'=>  "postmaster@sandboxd463ddd8ff2f4df2ac4178e18f86b3d4.mailgun.org",                // SMTP Username  
                'smtp_pass'=>  "ee43c2e1e0c4e9af89b69fd48a54d6ad",      // SMTP Password  
                'smtp_port'=>  "25",                // SMTP Port,default  
                'mailtype' => 'html',
                'wordwrap' => TRUE
                );  
        $this->load->library('email',$config);
        $this->email->set_newline("\r\n"); 
        $this->email->from('postmaster@sandboxd463ddd8ff2f4df2ac4178e18f86b3d4.mailgun.org', 'sporton');//from user's address 
        $this->email->to($email);   
        $this->email->subject("Email Verification");
        $verifylink = "http://54.69.110.214/index.php/mobile/verify?email=".$email."&token=".$verificationText;
        $content = "<!DOCTYPE html><html><head></head><body><p>Hi Sport-lover! </p><p>Welcome to SportON. You are just one click away from joining our dynamic community and getting your Sport-ON!</p><p>To begin, click <a href =".$verifylink.">here</a> to verify your email address.</p><p>Have fun, <br/>SportON Team</p></body></html>";
        
        $this->email->message($content);   
        if($this->email->send()){  
            log_message('debug','SportON: successful send email');
            return true;  
        }  
        else{  
            log_message('debug','SportON: Failed send email failed'.$email);
            log_message('debug',$this->email->print_debugger());
            return false;  
        }
    }
    
    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');
        log_message('debug','SportON in verify be called'.$email.". token is:".$token);
        $userData  = $this->mobile_model->getUserByEmailAndToken($email, $token);
        if(!empty($userData)) {
            $update = array('user_active_status'=>'1');
            $this->mobile_model->updateUser($userData->user_id,$update);
        }
        $this->load->view('jump');
    }
    /*
     * Symon Required to add Bot application which need below 2 apis to get information.
     */
    public function get_user_id_by_email() {
        $email = $this->input->get('email');
        $userData = $this->mobile_model->getUserByEmail($email);
        if(!empty($userData)){
            $user_id = $userData->user_id;
            $response['responseCode'] = 1;
            $response['id'] =$user_id;
            die(json_encode($response));
        } else {
            $response['responseCode'] = 0;
            $response['message'] = 'Invalid User Email.';
            die(json_encode($response));
        }
    }
    /*
     * Symon Required to add Bot application which need below 2 apis to get information.
     */
    public function get_email_by_user_id() {
        $id = $this->input->get('id');
        $user_id = intval($id);
        $userData = $this->mobile_model->getUserById($user_id);
        if(!empty($userData)){
            $email = $userData->user_email;
            $response['responseCode'] = 1;
            $response['email'] =$email;
            die(json_encode($response));
        } else {
            $response['responseCode'] = 0;
            $response['message'] = 'Invalid User Id.';
            die(json_encode($response));
        }
    }
}
