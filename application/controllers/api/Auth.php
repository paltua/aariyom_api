<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Auth extends CI_Controller {
    use REST_Controller { REST_Controller::__construct as private __resTraitConstruct; }

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['login_post']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['forgot_password_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['reset_password_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->load->model('auth_model');
    }

    public function login_post(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() === TRUE){
            $postData = [
                'user_email' => $this->post('email'),
                'user_pwd' => $this->post('password'),
            ];
            $retData = $this->_activation($postData);
            // Set the response and exit
            $responseData = [
                'status' => $retData['status'],
                'message' => $retData['msg'],
                'data' => ($retData['status'] === 'success'?$postData:'')
            ];
            $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($retData,  200); // OK (200) being the HTTP response code
        }else{
            // Set the response and exit
            $responseData = [
                'status' => 'danger',
                'message' => validation_errors(),
                'data' => '',
            ];
            $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($retData,  200); // OK (401) being the HTTP response code
        }
        
    }

    private function _activation($userMasterForm = array()){
        $userMasterData = $this->auth_model->get($userMasterForm);//checking with email & password
        $retData['status'] = '';
        $retData['msg'] = '';
        $retData['category'] = '';
        if(count($userMasterData) > 0){
            $data = $this->_checkActiveStatus($userMasterData, $userMasterForm);
            $retData['status'] = $data['status'];
            $retData['msg'] = $data['msg'];
        }else{
            $retData['status'] = 'danger';
            $retData['msg'] = "Invalid email id.";
        }
        if($retData['status'] != 'danger'){
        	$retData['category'] = '';
            // $this->_setSession($userMasterData);
        }
        return $retData;
    }

    private function _checkActiveStatus($userMasterData = array(),$userMasterForm = array()){
        $retData['status'] = '';
        $retData['msg'] = '';
        if(!password_verify($userMasterForm['user_pwd'],$userMasterData[0]->user_pwd)){
        	$retData['status'] = 'danger';
        	$retData['msg'] = 'Password is wrong.';
        }elseif($userMasterData[0]->user_status == 'inactive'){
        	$retData['status'] = 'danger';
        	$retData['msg'] = 'Your account is Inactive';
        }
        return $retData;
    }

    private function _setSession($userData = array()){
        $this->session->set_userdata('user_id', $userData[0]->user_id);
        $this->session->set_userdata('email', $userData[0]->user_email);
        $this->session->set_userdata('name', $userData[0]->name);
        $this->session->set_userdata('user_category', '');
        $this->session->set_userdata('user_status', $userData[0]->um_status);
        
    }

    public function forgot_password_post(){

    }

    public function reset_password_post(){

    }

}