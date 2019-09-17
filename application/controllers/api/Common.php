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

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['country_list_get']['limit'] = 1000; // 500 requests per hour per user/key
        $this->methods['state_list_get']['limit'] = 1000; // 100 requests per hour per user/key
        $this->methods['city_list_get']['limit'] = 1000; // 100 requests per hour per user/key
        $this->methods['programme_list_get']['limit'] = 1000;
        $this->load->model('tbl_generic_model');
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
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
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
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
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
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function programme_list_get()
    {
        // India 96
        $where = [];
        $select = '*';
        $orderBy['program_title'] = 'ASC';
        $data = $this->tbl_generic_model->get('programs', $select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }
}
