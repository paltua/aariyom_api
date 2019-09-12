<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

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
    use REST_Controller { REST_Controller::__construct as private __resTraitConstruct; }
    public $table = '';
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
        $this->load->model('tbl_generic_model');
        $this->table = 'programs';
    }

    public function list_post(){
        $where = array();
        $select = '*';
        $orderBy['program_id'] = 'DESC';
        $data = $this->tbl_generic_model->get($this->table,$select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

    public function add_post(){
        $this->load->library('form_validation');
        $data = [
            'program_title' => $this->post('program_title'),
            'program_desc' => $this->post('program_desc'),
            'created_by'=>$this->post('created_by'),
        ];
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('program_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('program_desc', 'Description', 'trim|required');
        if ($this->form_validation->run() === TRUE){
            $postData = $data;
            $insertId = $this->tbl_generic_model->add($this->table, $postData);
            // Set the response and exit
            if($insertId > 0){
                $responseData = [
                    'status' => 'success',
                    'message' => 'Added successfully.',
                    'data' => []
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (200) being the HTTP response code
            }else{
                // Set the response and exit
                $responseData = [
                    'status' => 'danger',
                    'message' => 'Sorry! Please try again.',
                    'data' => [],
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (401) being the HTTP response code
            }
            
        }else{
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

    public function update_post(){
        // India 96
        $this->load->library('form_validation');
        $data = [
            'program_title' => $this->post('program_title'),
            'program_desc' => $this->post('program_desc'),
            'created_by'=>$this->post('created_by'),
        ];
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('program_title', 'Title', 'trim|required');
        $this->form_validation->set_rules('program_desc', 'Description', 'required');
        if ($this->form_validation->run() === TRUE){
            $postData = $data;
            $insertId = $this->tbl_generic_model->add($this->table, $postData);
            // Set the response and exit
            if($insertId > 0){
                $responseData = [
                    'status' => 'success',
                    'message' => 'Added successfully.',
                    'data' => []
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (200) being the HTTP response code
            }else{
                // Set the response and exit
                $responseData = [
                    'status' => 'danger',
                    'message' => 'Sorry! Please try again.',
                    'data' => [],
                ];
                // $retData = AUTHORIZATION::generateToken($responseData);
                $this->response($responseData,  200); // OK (401) being the HTTP response code
            }
            
        }else{
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

    public function single_get(){
        // West Bengal 1627
        $where['program_id'] = $this->uri->segment(5);
        $select = '*';
        $orderBy = [];
        $data = $this->tbl_generic_model->get($this->table,$select, $where, $orderBy);
        $responseData = [
            'status' => 'success',
            'message' => count($data) > 0?'':'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken($responseData);
        $this->response($responseData,  200); // OK (200) being the HTTP response code
    }

}