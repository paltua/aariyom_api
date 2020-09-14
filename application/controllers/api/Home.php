<?php

use Restserver\Libraries\REST_Controller;

defined( 'BASEPATH' ) or exit( 'No direct script access allowed' );

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

class Home extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $data = array();

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['index_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['index_fu_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['index_event_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['index_event_slider_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['index_program_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['get_event_all_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['get_event_details_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['get_all_programs_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['getUpcomingEvent_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['getArchive_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['get_programme_details_get']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['get_fu_details_get']['limit'] = 500;

        $this->load->model( 'tbl_generic_model' );
        $this->load->model( 'event_model' );
        $this->load->model( 'fu_model' );
        $this->load->model( 'programme_model' );
    }

    public function index_get() {
        $this->data['fus'] = $this->fu_model->getDataForHome();
        $this->data['events'] = $this->event_model->getDataForHome();
        $this->data['programs'] = $this->programme_model->getDataForHome();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function index_fu_get() {
        $this->data = $this->fu_model->getDataForHome();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function index_event_get() {
        $this->data = $this->event_model->getDataForHome();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function index_event_slider_get() {
        $this->data = $this->event_model->getDataForHomeSlider();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function index_program_get() {
        $this->data = $this->programme_model->getDataForHome();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function get_event_all_get() {
        $this->data['list'] = $this->event_model->getDataForEvent();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function get_event_details_get() {
        $event_id = $this->uri->segment( 4 );
        $this->data['details'] = $this->event_model->getEventDetails( $event_id );
        $this->data['images'] = $this->event_model->getImages( $event_id );
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function get_all_programs_get() {
        $details = $this->programme_model->getData();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $details
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function getUpcomingEvent_get() {
        $this->data['list'] = $this->event_model->getUpcomingEvent();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function getArchive_get() {
        $this->data['list'] = $this->event_model->getArchive();
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $this->data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function get_programme_details_get() {
        // West Bengal 1627
        $programme_id = $this->uri->segment( 4 );
        $data['details'] = $this->programme_model->geSingleFrontEnd( $programme_id );
        $data['images'] = $this->programme_model->getImages( $programme_id );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function get_fu_details_get() {
        // West Bengal 1627
        $fu_id = $this->uri->segment( 4 );
        $data['details'] = $this->fu_model->getSingleFrontEnd( $fu_id );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

}