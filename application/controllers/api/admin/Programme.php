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

class Programme extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $table = '';
    public $imagePath = './'.IMAGE_PATH__PROG;

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
        $this->methods['delete_get']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->methods['image_list_get']['limit'] = 500;
        $this->methods['delete_image_get']['limit'] = 500;
        $this->methods['image_upload_post']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->load->model( 'tbl_generic_model' );
        $this->load->model( 'programme_model' );
        $this->table = 'programs';
    }

    public function list_post() {
        $postData = $this->post();
        $data = $this->programme_model->admin_list( $postData );
        $pagingData = [
            'recordsTotal' => $this->programme_model->admin_list_count(),
            'recordsFiltered' => $this->programme_model->admin_list_filter_count( $postData ),
            'list' => $data
        ];
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => $pagingData
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function add_post() {
        $this->load->library( 'form_validation' );
        $data = [
            'program_title' => $this->post( 'program_title' ),
            'program_desc' => $this->post( 'program_desc' ),
            'created_by' => $this->post( 'created_by' ),
            'program_status' => $this->post( 'program_status' ),
            'program_about' => $this->post( 'program_about' ),
            'program_objectives' => $this->post( 'program_objectives' ),
            'org_by_custom_name' => $this->post( 'org_by_custom_name' ),
            'program_short_desc' => $this->post( 'program_short_desc' ),
        ];
        $this->form_validation->set_data( $data );
        $this->form_validation->set_rules( 'program_title', 'Title', 'trim|required' );
        $this->form_validation->set_rules( 'program_short_desc', 'Short Description', 'trim|required' );
        if ( $this->form_validation->run() === TRUE ) {

            $postData = $data;
            $insertId = $this->tbl_generic_model->add( $this->table, $postData );
            $this->insertFuList( $insertId );
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
        $this->load->library( 'form_validation' );
        $data = [
            'program_title' => $this->post( 'program_title' ),
            'program_desc' => $this->post( 'program_desc' ),
            'created_by' => $this->post( 'created_by' ),
            'program_status' => $this->post( 'program_status' ),
            'program_about' => $this->post( 'program_about' ),
            'program_objectives' => $this->post( 'program_objectives' ),
            'org_by_custom_name' => $this->post( 'org_by_custom_name' ),
            'program_short_desc' => $this->post( 'program_short_desc' ),
        ];
        $this->form_validation->set_data( $data );
        $this->form_validation->set_rules( 'program_title', 'Title', 'trim|required' );
        $this->form_validation->set_rules( 'program_short_desc', 'Short Description', 'trim|required' );
        if ( $this->form_validation->run() === TRUE ) {
            $postData = $data;

            $where['program_id'] = $this->post( 'program_id' );
            $updateStatus = $this->tbl_generic_model->edit( $this->table, $postData, $where );
            $this->insertFuList( $this->post( 'program_id' ) );
            // Set the response and exit
            if ( $where['program_id'] > 0 && $updateStatus ) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Updated successfully.',
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
                // 'message' => validation_errors(),
                'message' => json_encode( $this->post() ),
                'data' => '',
            ];
            // $retData = AUTHORIZATION::generateToken( $responseData );
            $this->response( $responseData,  200 );
            // OK ( 401 ) being the HTTP response code
        }
    }

    private function insertFuList( $program_id = 0 ) {
        $fuList = $this->post( 'org_by' );
        if ( $program_id > 0 ) {
            if ( $fuList != '' ) {
                $this->tbl_generic_model->delete( 'programs_fus_rel', array( 'program_id' => $program_id ) );
                $insertData = [];
                $fuListArr = explode( ',', $fuList );
                foreach ( $fuListArr as $value ) {
                    $insertData[] = [
                        'program_id' => $program_id,
                        'fu_id' => $value,
                    ];
                }
                $this->tbl_generic_model->add_batch( 'programs_fus_rel', $insertData );
            }
        }
    }

    public function single_get() {
        // West Bengal 1627
        $where['program_id'] = $this->uri->segment( 5 );
        $data = $this->programme_model->geSingle( $where['program_id'] );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function image_list_get() {
        $program_id = $this->uri->segment( 5 );
        $data = $this->programme_model->getImages( $program_id );
        $responseData = [
            'status' => 'success',
            'message' => 'Listing successfully.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function delete_get() {
        $postData = [
            'is_deleted' => 'yes'
        ];
        $where['program_id'] = $this->uri->segment( 5 );
        $updateStatus = $this->tbl_generic_model->edit( $this->table, $postData, $where );
        // Set the response and exit
        if ( $where['program_id'] > 0 && $updateStatus ) {
            $responseData = [
                'status' => 'success',
                'message' => 'Deleted successfully.',
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
    }

    public function do_upload() {
        $config['upload_path']      = $this->imagePath;
        $new_name                   = time() . '.' . pathinfo( $_FILES['program_image']['name'], PATHINFO_EXTENSION );
        $config['file_name']        = $new_name;
        $config['allowed_types']    = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        $config['max_width']        = 1000;
        $config['max_height']       = 500;
        $this->load->library( 'upload', $config );
        $retData = [];
        if ( !$this->upload->do_upload( 'program_image' ) ) {
            $retData['error'] = $this->upload->display_errors();
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $path = $this->upload->data();
            $this->_resizeImage( $path['file_name'] );
            $retData['error'] = '';
        }
        return $retData;
    }

    private function _resizeImage( $imageName = '' ) {
        // echo '<pre>';
        // print_r( $imageName );
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->imagePath.$imageName;
        $config['new_image'] = $this->imagePath.'thumb';
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 250;
        $config['height']       = 125;
        $this->load->library( 'image_lib' );
        $this->image_lib->initialize( $config );
        $this->image_lib->resize();
        // if ( $this->image_lib->resize() ) {
        //     echo 'success';
        // } else {
        //     echo $this->image_lib->display_errors();
        // }
        // die;
    }

    public function image_upload_post() {
        $responseData = [];
        $program_id = $this->post( 'program_id' );
        $created_by = $this->post( 'created_by' );
        $is_completed = $this->post( 'is_completed' );
        // $fData = $_FILES;
        $fData = $this->do_upload( $program_id );
        if ( $fData['error'] === '' ) {
            $this->updateDefaultImage( $program_id );
            $addData['program_id'] = $program_id;
            $addData['prog_img_name'] = $fData['data']['file_name'];
            $addData['created_by'] = $created_by;
            $addData['is_default'] = '1';
            $addData['is_completed'] = $is_completed;
            $insertId = $this->tbl_generic_model->add( 'programs_images', $addData );
            if ( $insertId > 0 ) {
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

        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    private function updateDefaultImage( $program_id = 0 ) {
        $updateEventData['is_default'] = '0';
        $updateWhereData['program_id'] = $program_id;
        $this->tbl_generic_model->edit( 'programs_images', $updateEventData, $updateWhereData );
    }

    public function image_list_default_get() {
        $program_id = $this->uri->segment( 5 );
        $prog_img_id = $this->uri->segment( 6 );
        $this->updateDefaultImage( $program_id );
        $updateEventData['is_default'] = '1';
        $updateWhereData['program_id'] = $program_id;
        $updateWhereData['prog_img_id'] = $prog_img_id;
        $this->tbl_generic_model->edit( 'programs_images', $updateEventData, $updateWhereData );
        $responseData = [
            'status' => 'success',
            'message' => 'Successfully updated default Image.',
            'data' => ''
        ];
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function delete_image_get() {
        $program_id = $this->uri->segment( 5 );
        $prog_img_id = $this->uri->segment( 6 );
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => ''
        ];
        if ( $program_id > 0 && $prog_img_id > 0 ) {
            $where['program_id'] = $program_id;
            $where['prog_img_id'] = $prog_img_id;
            $data = $this->tbl_generic_model->get( 'programs_images', '*', $where );
            if ( count( $data ) > 0 ) {
                if ( $data[0]->prog_img_name !== '' ) {
                    $this->tbl_generic_model->unlinkImage( $this->imagePath . $data[0]->prog_img_name );
                    $this->tbl_generic_model->delete( 'programs_images', $where );
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
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }
}