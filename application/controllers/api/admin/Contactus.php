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
class Contactus extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $data = array();
    public $imagePath = './images/events/';
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['add_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['update_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['single_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['list_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['delete_get']['limit'] = 500; // 100 requests per hour per user/key
        $this->load->model('tbl_generic_model');
        $this->load->model('contactus_model');
        $this->table = 'event_master';
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
        $this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('desccription', 'Message', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $inData['name'] = $this->data['firstName'] . ' ' . $this->data['lastName'];
            $inData['email'] = $this->data['email'];
            $inData['mobile'] = $this->data['mobile'];
            $inData['desccription'] = $this->data['desccription'];
            $this->tbl_generic_model->add('contact_us', $inData);
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
                'message' => validation_errors(),
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (401) being the HTTP response code
        }
    }

    public function update_post()
    { }



    public function single_get()
    {
        // West Bengal 1627
        $event_id = $this->uri->segment(5);
        $data = $this->event_model->getSingle($event_id);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function list_post()
    {
        $postData = $this->post();
        $data = $this->contactus_model->admin_list($postData);
        $pagingData = [
            'recordsTotal' => $this->contactus_model->admin_list_count(),
            'recordsFiltered' => $this->contactus_model->admin_list_filter_count($postData),
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

    public function delete_get()
    {
        $event_id = $this->uri->segment(5);
        $data = [
            'event_is_deleted' => 'yes'
        ];
        $updateStatus = $this->tbl_generic_model->edit('event_master', $data, array('event_id' => $event_id));
        $responseData = [
            'status' => 'success',
            'message' => 'Deleted successfully.',
            'data' => []
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function image_list_get()
    {
        $event_id = $this->uri->segment(5);
        $data = $this->event_model->getImages($event_id);
        $responseData = [
            'status' => 'success',
            'message' => 'Listing successfully.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function image_upload_post()
    {
        $responseData = [];
        $event_id = $this->post('event_id');
        $created_by = $this->post('created_by');
        // $fData = $_FILES;
        $fData = $this->do_upload($event_id);
        if ($fData['error'] === "") {
            $this->updateDefaultImage($event_id);
            $addData['event_id'] = $event_id;
            $addData['ei_image_name'] = $fData['data']['file_name'];
            $addData['created_by'] = $created_by;
            $addData['is_default'] = '1';
            $insertId = $this->tbl_generic_model->add('event_images', $addData);
            if ($insertId > 0) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Uploaded successfully.',
                    'data' => $addData
                ];
            } else {
                $responseData = [
                    'status' => 'danger',
                    'message' => 'Sorry! There is some technical error.',
                    'data' => $addData
                ];
            }
        } else {
            $responseData = [
                'status' => 'danger',
                'message' => $fData['error'],
                'data' => $fData
            ];
        }

        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function do_upload($event_id = 0)
    {
        $config['upload_path']          = $this->imagePath;
        $new_name                   = $event_id . '_' . time() . '.' . pathinfo($_FILES["event_image"]['name'], PATHINFO_EXTENSION);
        $config['file_name']        = $new_name;
        $config['allowed_types']        = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        // $config['max_width']            = 1200;
        // $config['max_height']           = 400;
        $this->load->library('upload', $config);
        $retData = [];
        if (!$this->upload->do_upload('event_image')) {
            $retData['error'] = $this->upload->display_errors();
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $this->upload->data();
            $retData['error'] = '';
        }
        return $retData;
    }

    public function image_list_default_get()
    {
        $event_id = $this->uri->segment(5);
        $ei_id = $this->uri->segment(6);
        $this->updateDefaultImage($event_id);
        $updateEventData['is_default'] = '1';
        $updateWhereData['event_id'] = $event_id;
        $updateWhereData['ei_id'] = $ei_id;
        $this->tbl_generic_model->edit('event_images', $updateEventData, $updateWhereData);
        $responseData = [
            'status' => 'success',
            'message' => 'Successfully updated default Image.',
            'data' => ''
        ];
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    private function updateDefaultImage($event_id = 0)
    {
        $updateEventData['is_default'] = '0';
        $updateWhereData['event_id'] = $event_id;
        $this->tbl_generic_model->edit('event_images', $updateEventData, $updateWhereData);
    }

    public function delete_image_get()
    {
        $event_id = $this->uri->segment(5);
        $ei_id = $this->uri->segment(6);
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => ''
        ];
        if ($event_id > 0 && $ei_id > 0) {
            $where['event_id'] = $event_id;
            $where['ei_id'] = $ei_id;
            $data = $this->tbl_generic_model->get('event_images', '*', $where);
            if (count($data) > 0) {
                if ($data[0]->ei_image_name !== '') {
                    $this->tbl_generic_model->unlinkImage($this->imagePath . $data[0]->ei_image_name);
                    $this->tbl_generic_model->delete('event_images', $where);
                    $responseData = [
                        'status' => 'success',
                        'message' => 'Successfully Deleted the Image.',
                        'data' => ''
                    ];
                } else {
                    $responseData = [
                        'status' => 'warning',
                        'message' => 'Sorry! No Image path is found.',
                        'data' => ''
                    ];
                }
            } else {
                $responseData = [
                    'status' => 'warning',
                    'message' => 'Sorry! No Data found.',
                    'data' => ''
                ];
            }
        } else {
            $responseData = [
                'status' => 'warning',
                'message' => 'Sorry! No Image is selected.',
                'data' => ''
            ];
        }
        $this->response($responseData,  200); // OK (200) being the HTTP response code
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
