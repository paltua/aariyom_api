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
class Event extends CI_Controller {
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
        $this->load->model('tbl_generic_model');
    }

    public function insert_post(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() === TRUE){
            $postData = [
                'user_email' => $this->post('email'),
                'user_pwd' => $this->post('password'),
            ];
            $retData = [];
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

    public function update_post(){
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

    public function single_get(){
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

    public function listing_post(){
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

    private function verify_request(){
        // Get all the headers
        $headers = $this->input->request_headers();

        // Extract the token
        $token = $headers['Authorization'];

        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED; //401
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                $this->response($response, $status);

                exit();
            } else {
                return $data;
            }
        } catch (Exception $e) {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED; //401
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            $this->response($response, $status);
        }
}

}