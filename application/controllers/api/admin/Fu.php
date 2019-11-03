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

    public function list_post()
    {
        $postData = $this->post();
        $data = $this->fu_model->admin_list($postData);
        $pagingData = [
            'recordsTotal' => $this->fu_model->admin_list_count(),
            'recordsFiltered' => $this->fu_model->admin_list_filter_count($postData),
            'list' => $data
        ];
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $pagingData
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('fu_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('fu_desc', 'Description', 'trim|required');
        // $this->form_validation->set_rules('fu_image_name', 'Image', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $fData = $this->do_upload();
            // Set the response and exit
            if ($fData['error'] === "") {
                $inData['fu_title'] = $inLogData['fu_title'] = $this->data['fu_title'];
                $inData['fu_desc'] = $inLogData['fu_desc'] = $this->data['fu_desc'];
                $inData['fu_status'] = $inLogData['fu_status'] = $this->data['fu_status'];
                $inData['fu_managed_by'] = $inLogData['fu_managed_by'] = $this->data['fu_managed_by'];
                $inData['fu_operating_location'] = $inLogData['fu_operating_location'] = $this->data['fu_operating_location'];
                $inData['fu_image'] = $inLogData['fu_image'] = $fData['data']['file_name'];
                $inLogData['fu_created_by'] = $this->data['fu_created_by'];
                $fu_id = $this->tbl_generic_model->add('functional_units', $inData);
                $inLogData['fu_id'] = $fu_id;
                $this->tbl_generic_model->add('functional_units_log', $inLogData);
                $responseData = [
                    'status' => 'success',
                    'message' => 'Added successfully.',
                    'data' => []
                ];
            } else {
                $responseData = [
                    'status' => 'danger',
                    'message' => $fData['error'],
                    'data' => $_FILES
                ];
            }
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (200) being the HTTP response code
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

    public function do_upload()
    {
        $config['upload_path']          = './images/fus/';
        $new_name                   = time() . '.' . pathinfo($_FILES["fu_image_name"]['name'], PATHINFO_EXTENSION);
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

    public function single_get()
    {
        $fu_id = $this->uri->segment(5);
        $data = $this->fu_model->getSingle($fu_id);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function update_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('fu_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('fu_desc', 'Description', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $inData = array();
            $fData['error'] = '';
            $inData['fu_image'] = '';
            $uploadStatus = 0;
            if ($_FILES) {
                $uploadStatus = 1;
                $fData = $this->do_upload();
                $inData['fu_image'] = $inLogData['fu_image'] = $fData['data']['file_name'];
            } else {
                $inData['fu_image'] = $inLogData['fu_image'] = $this->data['old_image_name'];
            }

            // Set the response and exit
            if ($fData['error'] === "") {
                $whereData['fu_id'] = $fu_id = $this->data['fu_id'];
                $inData['fu_title'] = $inLogData['fu_title'] = $this->data['fu_title'];
                $inData['fu_desc'] = $inLogData['fu_desc'] = $this->data['fu_desc'];
                $inData['fu_status'] = $inLogData['fu_status'] = $this->data['fu_status'];
                $inData['fu_managed_by'] = $inLogData['fu_managed_by'] = $this->data['fu_managed_by'];
                $inData['fu_operating_location'] = $inLogData['fu_operating_location'] = $this->data['fu_operating_location'];
                $inLogData['fu_created_by'] = $this->data['fu_created_by'];
                $this->tbl_generic_model->edit('functional_units', $inData, $whereData);
                $inLogData['fu_id'] = $fu_id;
                $this->tbl_generic_model->add('functional_units_log', $inLogData);
                if ($this->data['old_image_name'] !== '' && $uploadStatus === 1) {
                    $this->tbl_generic_model->unlinkImage('./images/fus/' . $this->data['old_image_name']);
                }
                $responseData = [
                    'status' => 'success',
                    'message' => 'Updated successfully.',
                    'data' => []
                ];
            } else {
                $responseData = [
                    'status' => 'danger',
                    'message' => $fData['error'],
                    'data' => $_FILES
                ];
            }
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (200) being the HTTP response code
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

    public function delete_get()
    {
        $fu_id = $this->uri->segment(5);
        $data = [
            'fu_is_deleted' => 'yes'
        ];
        $updateStatus = $this->tbl_generic_model->edit('functional_units', $data, array('fu_id' => $fu_id));
        $responseData = [
            'status' => 'success',
            'message' => 'Deleted successfully.',
            'data' => []
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }
}
