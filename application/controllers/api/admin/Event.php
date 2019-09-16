<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

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
class Event extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['add_post']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['update_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['single_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['listing_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['delete_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->load->model('tbl_generic_model');
        $this->load->model('event_model');
        $this->table = 'event_master';
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $data = [
            'event_title' => $this->post('event_title'),
            'event_long_desc' => $this->post('event_long_desc'),
            'event_created_by' => $this->post('event_created_by'),
            'event_about' => $this->post('event_about'),
            'event_objectives' => $this->post('event_objectives'),
            'event_start_date_time' => $this->post('event_start_date_time'),
            'event_end_date_time' => $this->post('event_end_date_time'),
            'event_code' => $this->post('event_code'),
        ];
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('event_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('event_long_desc', 'Description', 'trim|required');
        $this->form_validation->set_rules('event_about', 'About of Event', 'trim|required');
        $this->form_validation->set_rules('event_objectives', 'Event objectives', 'trim|required');
        $this->form_validation->set_rules('event_start_date_time', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('event_end_date_time', 'End Date', 'trim|required');

        if ($this->form_validation->run() === TRUE) {
            $postData = $data;
            $insertId = $this->tbl_generic_model->add($this->table, $postData);
            // Set the response and exit
            if ($insertId > 0) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Added successfully.',
                    'data' => []
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (200) being the HTTP response code
            } else {
                // Set the response and exit
                $responseData = [
                    'status' => 'danger',
                    'message' => 'Sorry! Please try again.',
                    'data' => [],
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (401) being the HTTP response code
            }
        } else {
            // Set the response and exit
            $responseData = [
                'status' => 'danger',
                'message' => validation_errors(),
                'data' => '',
            ];
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (401) being the HTTP response code
        }
    }

    public function update_post()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $postData = [
                'user_email' => $this->post('email'),
                'user_pwd' => $this->post('password'),
            ];
            $retData = $this->_activation($postData);
            // Set the response and exit
            $responseData = [
                'status' => $retData['status'],
                'message' => $retData['msg'],
                'data' => ($retData['status'] === 'success' ? $postData : '')
            ];
            $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($retData,  200); // OK (200) being the HTTP response code
        } else {
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

    public function single_get()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $postData = [
                'user_email' => $this->post('email'),
                'user_pwd' => $this->post('password'),
            ];
            $retData = $this->_activation($postData);
            // Set the response and exit
            $responseData = [
                'status' => $retData['status'],
                'message' => $retData['msg'],
                'data' => ($retData['status'] === 'success' ? $postData : '')
            ];
            $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($retData,  200); // OK (200) being the HTTP response code
        } else {
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

    public function listing_post()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $postData = [
                'user_email' => $this->post('email'),
                'user_pwd' => $this->post('password'),
            ];
            $retData = $this->_activation($postData);
            // Set the response and exit
            $responseData = [
                'status' => $retData['status'],
                'message' => $retData['msg'],
                'data' => ($retData['status'] === 'success' ? $postData : '')
            ];
            $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($retData,  200); // OK (200) being the HTTP response code
        } else {
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

    private function verify_request()
    {
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
