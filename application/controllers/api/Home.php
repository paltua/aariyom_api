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
class Home extends CI_Controller
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
        $this->methods['index_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['get_event_all_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->load->model('tbl_generic_model');
        $this->load->model('event_model');
        $this->load->model('fu_model');
        $this->load->model('programme_model');
    }

    public function index_get()
    {
        $this->data['fus'] = $this->fu_model->getDataForHome();
        $this->data['events'] = $this->event_model->getDataForHome();
        $this->data['programs'] = $this->programme_model->getDataForHome();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }


    public function get_event_all_get()
    {
        $this->data['list'] = $this->event_model->getEventForEvent();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }
}
