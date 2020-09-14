<?php
use Restserver\Libraries\REST_Controller;
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

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

class Programme extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['add_post']['limit'] = 500;
        // 500 requests per hour per user/key
        $this->methods['update_post']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->methods['single_get']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->methods['list_post']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->load->model( 'tbl_generic_model' );
        $this->load->model( 'programme_model' );
    }

    public function list_post() {
        $where = array();
        $select = '*';
        $orderBy['program_id'] = 'DESC';
        $data = $this->tbl_generic_model->get( 'programs', $select, $where, $orderBy );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function add_post() {
        $this->load->library( 'form_validation' );
        $this->form_validation->set_rules( 'program_title', 'Title', 'trim|required' );
        $this->form_validation->set_rules( 'programe_desc', 'Description', 'trim|required' );
        if ( $this->form_validation->run() === TRUE ) {
            $postData = [
                'program_title' => $this->post( 'program_title' ),
                'programe_desc' => $this->post( 'programe_desc' ),
                'created_by'=>$this->post( 'created_by' ),
            ];
            $insertId = $this->tbl_generic_model->add( 'programs', $postData );
            // Set the response and exit
            if ( $insertId > 0 ) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Added successfully.',
                    'data' => []
                ];
                // $retData = AUTHORIZATION::generateToken( $responseData );
                $this->response( $responseData,  200 );
                // OK ( 200 ) being the HTTP response code
            } else {
                // Set the response and exit
                $responseData = [
                    'status' => 'danger',
                    'message' => 'Sorry! Please try again.',
                    'data' => [],
                ];
                // $retData = AUTHORIZATION::generateToken( $responseData );
                $this->response( $responseData,  200 );
                // OK ( 401 ) being the HTTP response code
            }

        } else {
            // Set the response and exit
            $responseData = [
                'status' => 'danger',
                'message' => validation_errors(),
                'data' => '',
            ];
            // $retData = AUTHORIZATION::generateToken( $responseData );
            $this->response( $responseData,  200 );
            // OK ( 401 ) being the HTTP response code
        }
    }

    public function update_post() {
        // India 96
        $where['country_id'] = $this->uri->segment( 4 );
        $select = '*';
        $orderBy['name'] = 'ASC';
        $data = $this->tbl_generic_model->get( 'regions', $select, $where, $orderBy );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function single_get() {
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

}