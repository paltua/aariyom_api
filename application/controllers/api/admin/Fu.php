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
class Fu extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $data = array();

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
        $this->methods['list_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['delete_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['image_upload_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['image_list_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->load->model('tbl_generic_model');
        $this->load->model('fu_model');
        $this->table = 'event_master';
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('fu_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('fu_desc', 'Description', 'trim|required');
        // $this->form_validation->set_rules('fu_image_name_valid', 'Image', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $fu_id = 1; //$this->_eventMasterDataAdd();
            // Set the response and exit
            if ($fu_id > 0) {
                $fData = $this->do_upload($fu_id);
                if ($fData['error'] === "") {
                    $responseData = [
                        'status' => 'success',
                        'message' => 'Added successfully.',
                        'data' => $_FILES
                    ];
                } else {
                    $responseData = [
                        'status' => 'warning',
                        'message' => 'Added successfully.But Image is not updated.Error as below.' . $fData['error'],
                        'data' => $_FILES
                    ];
                }
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
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (401) being the HTTP response code
        }
    }

    public function do_upload($fu_id = 0)
    {
        $config['upload_path']          = './images/fus/';
        $new_name                   = $fu_id . '_' . time() . '.' . pathinfo($_FILES["fu_image_name"]['name'], PATHINFO_EXTENSION);
        $config['file_name']        = $new_name;
        $config['allowed_types']        = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        // $config['max_width']            = 1200;
        // $config['max_height']           = 400;
        $this->load->library('upload', $config);
        $retData = [];
        if (!$this->upload->do_upload('fu_image_name')) {
            $retData['error'] = $this->upload->display_errors();
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $this->upload->data();
            $retData['error'] = '';
        }
        return $retData;
    }
}
