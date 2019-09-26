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
        $this->load->model('event_model');
        $this->table = 'event_master';
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('event_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('programs', 'Programs', 'trim|callback_valid_multiple_select');
        $this->form_validation->set_rules('event_start_date', 'Start Date', 'trim|callback_valid_start_date');
        $this->form_validation->set_rules('event_end_date', 'End Date', 'trim|callback_valid_end_date');
        $this->form_validation->set_rules('event_long_desc', 'Description', 'trim|required');
        $this->form_validation->set_rules('event_about', 'About of Event', 'trim|required');
        $this->form_validation->set_rules('event_objectives', 'Event objectives', 'trim|required');
        $this->form_validation->set_rules('country_id', 'Country', 'trim|required');
        $this->form_validation->set_rules('region_id', 'State', 'trim|required');
        $this->form_validation->set_rules('address', 'Address(Street/Road/House No)', 'trim|required');
        $this->form_validation->set_rules('pin', 'Pin', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $insertId = $this->_eventMasterDataAdd();
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
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (401) being the HTTP response code
        }
    }

    private function _eventMasterDataAdd()
    {
        $this->_setDateTime();
        $eventMasterDetails = [
            'event_title' => $this->data['event_title'],
            'event_long_desc' => $this->data['event_long_desc'],
            'event_about' => $this->data['event_about'],
            'event_code' => rand(),
            'event_objectives' => $this->data['event_objectives'],
            'event_start_date_time' => $this->data['event_start_date_time'],
            'event_end_date_time' => $this->data['event_end_date_time'],
            'event_created_by' => $this->data['event_created_by'],
        ];
        $event_id = $this->tbl_generic_model->add('event_master', $eventMasterDetails);
        if ($event_id > 0) {
            $this->_addEventMasterLog($event_id);
            $epr_id = $this->_addPrograms($event_id);
            $el_id = $this->_addLocation($event_id);
            $updateEventData = array(
                'epr_id' => $epr_id,
                'el_id' => $el_id,
                'event_code' => 'EVENT' . $event_id,
            );
            $updateWhereData = array(
                'event_id' => $event_id
            );
            $this->tbl_generic_model->edit('event_master', $updateEventData, $updateWhereData);
        }
        return $event_id;
    }

    public function valid_multiple_select()
    {
        if (empty($this->post('programs'))) {
            $this->form_validation->set_message('valid_multiple_select', 'Please select at least one Programme');
            return false;
        } else {
            return true;
        }
    }

    public function valid_start_date()
    {
        if (empty($this->post('event_start_date'))) {
            $this->form_validation->set_message('valid_start_date', 'The Start Date field is required.');
            return false;
        } else {
            return true;
        }
    }

    public function valid_end_date()
    {
        if (empty($this->post('event_end_date'))) {
            $this->form_validation->set_message('valid_end_date', 'The End Date field is required.');
            return false;
        } else {
            return true;
        }
    }

    private function _setDateTime()
    {
        $startDate = $this->data['event_start_date'];
        $endDate = $this->data['event_end_date'];
        $stime = $this->data['event_start_time'];
        $etime = $this->data['event_end_time'];
        $sdt = $startDate['year'] . '-' . $startDate['month'] . '-' . $startDate['day'];
        if ($stime != '') {
            $sdt .= ' ' . $stime['hour'] . ':' . $stime['minute'] . ':00';
        }
        $edt = $endDate['year'] . '-' . $endDate['month'] . '-' . $endDate['day'];
        if ($etime != '') {
            $edt .= ' ' . $etime['hour'] . ':' . $etime['minute'] . ':00';
        }
        $this->data['event_start_date_time'] = date_format(new DateTime($sdt), 'Y-m-d H:i:s');
        $this->data['event_end_date_time'] = date_format(new DateTime($edt), 'Y-m-d H:i:s');
    }

    private function _addEventMasterLog($event_id = 0)
    {
        $eventMasterDetailsLog = [
            'event_title' => $this->data['event_title'],
            'event_long_desc' => $this->data['event_long_desc'],
            'event_about' => $this->data['event_about'],
            'event_objectives' => $this->data['event_objectives'],
            'event_start_date_time' => $this->data['event_start_date_time'],
            'event_end_date_time' => $this->data['event_end_date_time'],
            'event_created_by' => $this->data['event_created_by'],
            'event_id' => $event_id,
        ];
        $this->tbl_generic_model->add('event_master_log', $eventMasterDetailsLog);
    }

    private function _addPrograms($event_id = 0)
    {
        $epr_id = 0;
        if (count($this->data['programs']) > 0) {
            $eventProgramsRel = [
                'event_id' => $event_id,
            ];
            $epr_id = $this->tbl_generic_model->add('event_programs_rel', $eventProgramsRel);
            $eventProgramsRelRel = [];

            foreach ($this->data['programs'] as $key => $value) {
                $eventProgramsRelRel[] = [
                    'epr_id' => $epr_id,
                    'program_id' => $value,
                ];
            }
            // print_r($eventProgramsRelRel);
            $this->tbl_generic_model->add_batch('event_programs_rel_rel', $eventProgramsRelRel);
        }
        return $epr_id;
    }

    private function _addLocation($event_id = 0)
    {
        $eventLocation = [
            'event_id' => $event_id,
            'country_id' => $this->data['country_id'],
            'region_id' => $this->data['region_id'],
            'city_id' => $this->data['city_id'],
            'address' => $this->data['address'],
            'pin' => $this->data['pin'],
        ];
        return $this->tbl_generic_model->add('event_location', $eventLocation);
    }

    public function update_post()
    {
        $this->load->library('form_validation');
        $this->data = $this->post();
        $this->form_validation->set_data($this->data);
        $this->form_validation->set_rules('event_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('programs', 'Programs', 'trim|callback_valid_multiple_select');
        $this->form_validation->set_rules('event_start_date', 'Start Date', 'trim|callback_valid_start_date');
        $this->form_validation->set_rules('event_end_date', 'End Date', 'trim|callback_valid_end_date');
        $this->form_validation->set_rules('event_long_desc', 'Description', 'trim|required');
        $this->form_validation->set_rules('event_about', 'About of Event', 'trim|required');
        $this->form_validation->set_rules('event_objectives', 'Event objectives', 'trim|required');
        $this->form_validation->set_rules('country_id', 'Country', 'trim|required');
        $this->form_validation->set_rules('region_id', 'State', 'trim|required');
        $this->form_validation->set_rules('address', 'Address(Street/Road/House No)', 'trim|required');
        $this->form_validation->set_rules('pin', 'Pin', 'trim|required');
        if ($this->form_validation->run() === TRUE) {
            $insertId = $this->_eventMasterDataUpdate();
            // Set the response and exit
            if ($insertId > 0) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Updated successfully.',
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
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken($responseData);
            $this->response($responseData,  200); // OK (401) being the HTTP response code
        }
    }

    private function _eventMasterDataUpdate()
    {
        $this->_setDateTime();
        $eventMasterDetails = [
            'event_title' => $this->data['event_title'],
            'event_long_desc' => $this->data['event_long_desc'],
            'event_about' => $this->data['event_about'],
            'event_objectives' => $this->data['event_objectives'],
            'event_start_date_time' => $this->data['event_start_date_time'],
            'event_end_date_time' => $this->data['event_end_date_time'],
            'event_created_by' => $this->data['event_created_by'],
        ];
        $event_id = $this->data['event_id'];
        $this->tbl_generic_model->edit('event_master', $eventMasterDetails, array('event_id' => $event_id));
        if ($event_id > 0) {
            $this->_addEventMasterLog($event_id);
            $epr_id = $this->_addPrograms($event_id);
            $el_id = $this->_addLocation($event_id);
            $updateEventData = array(
                'epr_id' => $epr_id,
                'el_id' => $el_id,
            );
            $updateWhereData = array(
                'event_id' => $event_id
            );
            $this->tbl_generic_model->edit('event_master', $updateEventData, $updateWhereData);
        }
        return $event_id;
    }

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
        $data = $this->event_model->admin_list($postData);
        $pagingData = [
            'recordsTotal' => $this->event_model->admin_list_count(),
            'recordsFiltered' => $this->event_model->admin_list_filter_count($postData),
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
            $addData['event_id'] = $event_id;
            $addData['ei_image_name'] = $fData['data']['file_name'];
            $addData['created_by'] = $created_by;
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
        $config['upload_path']          = './images/events/';
        $new_name                   = $event_id . '_' . time() . '.' . pathinfo($_FILES["event_image"]['name'], PATHINFO_EXTENSION);
        $config['file_name']        = $new_name;
        $config['allowed_types']        = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        // $config['max_width']            = 1200;
        // $config['max_height']           = 400;
        $this->load->library('upload', $config);
        $retData = [];
        if (!$this->upload->do_upload('event_image')) {
            $retData['error'] = array('error' => $this->upload->display_errors());
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $this->upload->data();
            $retData['error'] = '';
        }
        return $retData;
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
