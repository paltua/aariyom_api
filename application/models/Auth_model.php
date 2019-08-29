<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model {

    function __construct() {
        // parent::__construct();
		log_message('INFO', 'Auth_model enter');
    }
	
	/*
	 * function get()
	 * this function is used for fetching user details with respect to email and password
	 * @param : Array
	 * @return : Array
	 */
    public function get($where = array()){
		$this->db->select('AUM.*');
		$this->db->from('user_master AUM');
		$this->db->where('AUM.user_email',$where['user_email']);
		$query = $this->db->get();
		return $query->result();
	}
	
	
	
	public function getUserByUserId($user_id = 0){
		$this->db->select('UM.*');
		$this->db->from('web_user_master UM');
		$this->db->where('UM.user_id',$user_id);
		$query = $this->db->get();
		return $query->result();
	}

	
	
    
}