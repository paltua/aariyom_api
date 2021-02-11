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

class Common extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $imagePath = './images/about-us/';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['country_list_get']['limit'] = 1000;
        // 500 requests per hour per user/key
        $this->methods['state_list_get']['limit'] = 1000;
        // 100 requests per hour per user/key
        $this->methods['city_list_get']['limit'] = 1000;
        // 100 requests per hour per user/key
        $this->methods['programme_list_get']['limit'] = 1000;
        $this->methods['dashboard_details_get']['limit'] = 1000;
        $this->methods['settings_get']['limit'] = 1000;
        $this->methods['settings_update_post']['limit'] = 1000;
        $this->load->model('tbl_generic_model');
        $this->load->model('event_model');
        $this->load->model('programme_model');
        $this->load->model('fu_model');
    }

    public function country_list_get()
    {
        $where = array();
        $select = '*';
        $orderBy['name'] = 'ASC';
        $data = $this->tbl_generic_model->get('countries', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function state_list_get()
    {
        // India 96
        $where['country_id'] = $this->uri->segment(4);
        $select = '*';
        $orderBy['name'] = 'ASC';
        $data = $this->tbl_generic_model->get('regions', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function city_list_get()
    {
        // West Bengal 1627
        $where['region_id'] = $this->uri->segment(4);
        $select = '*';
        $orderBy['name'] = 'ASC';
        $data = $this->tbl_generic_model->get('cities', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function programme_list_get()
    {
        // India 96
        $where['is_deleted'] = 'no';
        $select = '*';
        $orderBy['program_title'] = 'ASC';
        $data = $this->tbl_generic_model->get('programs', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function fu_list_get()
    {
        // India 96
        $where['fu_is_deleted'] = 'no';
        $select = '*';
        $orderBy['fu_title'] = 'ASC';
        $data = $this->tbl_generic_model->get('functional_units', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function dashboard_details_get()
    {
        $data[0] = [];
        $where['is_deleted'] = 'no';
        $data[0]['label'] = 'Programs';
        $data[0]['bgClass'] = 'primary';
        $data[0]['icon'] = 'fa-comments';
        $data[0]['url'] = '/admin/programs';
        $data[0]['count'] = $this->programme_model->admin_list_count();
        $where = [];
        $where['event_is_deleted'] = 'no';
        $data[1]['label'] = 'Events';
        $data[1]['bgClass'] = 'warning';
        $data[1]['icon'] = 'fa-tasks';
        $data[1]['url'] = '/admin/events';
        $data[1]['count'] = $this->event_model->admin_list_count();
        $where = [];
        $where['fu_is_deleted'] = 'no';
        $data[2]['label'] = 'Functional Units';
        $data[2]['bgClass'] = 'success';
        $data[2]['icon'] = 'fa-shopping-cart';
        $data[2]['url'] = '/admin/functional-units';
        $data[2]['count'] = $this->fu_model->admin_list_count();
        // $where = [];
        // $where['is_deleted'] = 'no';
        // $programs = $this->tbl_generic_model->countWhere( 'programs', $where );
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function settings_get()
    {
        // West Bengal 1627
        $where['page'] = $this->uri->segment(4);
        $select = '*';
        $orderBy = [];
        $data = [];
        $dbData = $this->tbl_generic_model->get('settings', $select, $where, $orderBy);
        if (count($dbData) > 0) {
            foreach ($dbData as $key => $value) {
                if ($value->key_name === 'image') {
                    $data[$value->key_name] = $value->key_value;
                    $data['image_path'] = base_url('images/about-us/') . $value->key_value;
                } else {
                    $data[$value->key_name] = $value->key_value;
                }
            }
        }

        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    public function settings_update_post()
    {
        // print_r($_FILES);
        // print_r($_POST);
        foreach ($this->post() as $key => $value) {
            if ($key !== 'page') {
                if ($key !== 'old_image') {
                    $where['key_name'] = $key;
                    $data['key_value'] = $value;
                    $this->tbl_generic_model->edit('settings', $data, $where);
                } else {
                    if ($_FILES) {
                        $imageData = $this->uploadSettingsImage('about-us', $value);
                        if ($imageData['error'] === '') {
                            $where['key_name'] = 'image';
                            $data['key_value'] = $imageData['data']['file_name'];
                            $this->tbl_generic_model->edit('settings', $data, $where);
                        }
                    }
                }
            }
        }
        $responseData = [
            'status' => 'success',
            'message' => 'updated',
            'data' => [$where, $data]
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response($responseData,  200);
        // OK ( 200 ) being the HTTP response code
    }

    private function uploadSettingsImage($page = 'about-us', $imageData = '')
    {
        $event_id = 1;
        $config['upload_path']          = $this->imagePath;
        $new_name                   = $event_id . '_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $config['file_name']        = $new_name;
        $config['allowed_types']        = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        // $config['max_width']            = 1200;
        // $config['max_height']           = 400;
        $this->load->library('upload', $config);
        $retData = [];
        if (!$this->upload->do_upload('image')) {
            $retData['error'] = $this->upload->display_errors();
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $this->upload->data();
            $retData['error'] = '';
        }
        return $retData;
    }
}