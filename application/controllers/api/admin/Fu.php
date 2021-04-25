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

class Fu extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }
    public $data = array();
    public $imagePath = './' . IMAGE_PATH__FU;

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
        $this->methods['image_upload_post']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->methods['image_list_get']['limit'] = 500;

        $this->methods['delete_image_get']['limit'] = 500;
        // 100 requests per hour per user/key
        $this->load->model( 'tbl_generic_model' );
        $this->load->model( 'fu_model' );
        $this->table = 'event_master';
    }

    public function list_post() {
        $postData = $this->post();
        $data = $this->fu_model->admin_list( $postData );
        $pagingData = [
            'recordsTotal' => $this->fu_model->admin_list_count(),
            'recordsFiltered' => $this->fu_model->admin_list_filter_count( $postData ),
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
        $this->data = $this->post();
        $this->form_validation->set_data( $this->data );
        $this->form_validation->set_rules( 'fu_title', 'Title', 'trim|required' );
        $this->form_validation->set_rules( 'fu_short_desc', 'Description', 'trim|required' );
        // $this->form_validation->set_rules( 'fu_img_name', 'Image', 'trim|required' );
        if ( $this->form_validation->run() === TRUE ) {
            $inData['fu_title'] = $inLogData['fu_title'] = $this->data['fu_title'];
            $inData['fu_desc'] = $inLogData['fu_desc'] = $this->data['fu_desc'];
            $inData['fu_short_desc'] = $inLogData['fu_short_desc'] = $this->data['fu_short_desc'];
            $inData['fu_about'] = $inLogData['fu_about'] = $this->data['fu_about'];
            $inData['fu_objectives'] = $inLogData['fu_objectives'] = $this->data['fu_objectives'];
            $inData['fu_status'] = $inLogData['fu_status'] = $this->data['fu_status'];
            $inData['fu_managed_by'] = $inLogData['fu_managed_by'] = $this->data['fu_managed_by'];
            $inData['fu_operating_location'] = $inLogData['fu_operating_location'] = $this->data['fu_operating_location'];
            $inData['fu_title_url'] = $inLogData['fu_title_url'] = url_title( $this->data['fu_title'] ) . '-' . $this->_generateString();
            $inData['fu_image'] = '';
            $inLogData['fu_created_by'] = $this->data['fu_created_by'];
            $fu_id = $this->tbl_generic_model->add( 'functional_units', $inData );
            $inLogData['fu_id'] = $fu_id;
            $this->tbl_generic_model->add( 'functional_units_log', $inLogData );
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
                'message' => validation_errors(),
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken( $responseData );
            $this->response( $responseData,  200 );
            // OK ( 401 ) being the HTTP response code
        }
    }

    private function _generateString( $digit = 6 ) {
        $total_characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        $randomString = '';
        for ( $i = 0; $i < $digit; $i++ ) {
            $index = rand( 0, strlen( $total_characters ) - 1 );
            $randomString .= $total_characters[$index];
        }
        return strtolower( $randomString );
    }

    public function do_upload( $fu_id = 0 ) {
        $retData = [];
        $retData['data'] = '';
        $retData['error'] = '';
        $config['upload_path']      = $this->imagePath;
        $new_name                   = time() . '.' . pathinfo( $_FILES['fu_img_name']['name'], PATHINFO_EXTENSION );
        $config['file_name']        = $new_name;
        $config['allowed_types']    = 'jpeg|gif|jpg|png';
        // $config['max_size']             = 1024;
        $config['min_width']            = 500;
        $config['min_height']           = 250;
        $this->load->library( 'upload', $config );

        if ( !$this->upload->do_upload( 'fu_img_name' ) ) {
            $retData['error'] = $this->upload->display_errors();
            $retData['data'] = $this->upload->data();
        } else {
            $retData['data'] = $path = $this->upload->data();
            $this->_resizeImage( $path['file_name'], '1000', '500', '' );
            $this->_resizeImage( $path['file_name'], '300', '150', 'thumb' );
            $retData['error'] = '';
        }

        return $retData;
    }

    private function _resizeImage( $imageName = '', $width = '1000', $height = '500', $folder = '' ) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->imagePath . $imageName;
        $config['new_image'] = $this->imagePath . $folder;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = $width;
        $config['height']       = $height;
        $this->load->library( 'image_lib' );
        $this->image_lib->initialize( $config );
        $this->image_lib->resize();
    }

    public function single_get() {
        $fu_id = $this->uri->segment( 5 );
        $data = $this->fu_model->getSingle( $fu_id );
        $responseData = [
            'status' => 'success',
            'message' => count( $data ) > 0 ? '' : 'No data please.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function update_post() {
        $this->load->library( 'form_validation' );
        $this->data = $this->post();
        $this->form_validation->set_data( $this->data );
        $this->form_validation->set_rules( 'fu_title', 'Title', 'trim|required' );
        $this->form_validation->set_rules( 'fu_short_desc', 'Description', 'trim|required' );
        if ( $this->form_validation->run() === TRUE ) {
            $inData = array();
            $whereData['fu_id'] = $fu_id = $this->data['fu_id'];
            $inData['fu_title'] = $inLogData['fu_title'] = $this->data['fu_title'];
            $inData['fu_desc'] = $inLogData['fu_desc'] = $this->data['fu_desc'];
            $inData['fu_short_desc'] = $inLogData['fu_short_desc'] = $this->data['fu_short_desc'];
            $inData['fu_about'] = $inLogData['fu_about'] = $this->data['fu_about'];
            $inData['fu_objectives'] = $inLogData['fu_objectives'] = $this->data['fu_objectives'];
            $inData['fu_status'] = $inLogData['fu_status'] = $this->data['fu_status'];
            $inData['fu_managed_by'] = $inLogData['fu_managed_by'] = $this->data['fu_managed_by'];
            $inData['fu_operating_location'] = $inLogData['fu_operating_location'] = $this->data['fu_operating_location'];
            $inData['fu_title_url'] = $inLogData['fu_title_url'] = url_title( $this->data['fu_title'] ) . '-' . $this->_generateString();
            $inLogData['fu_created_by'] = $this->data['fu_created_by'];
            $this->tbl_generic_model->edit( 'functional_units', $inData, $whereData );
            $inLogData['fu_id'] = $fu_id;
            $this->tbl_generic_model->add( 'functional_units_log', $inLogData );
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
                'message' => validation_errors(),
                'data' => [],
            ];
            // $retData = AUTHORIZATION::generateToken( $responseData );
            $this->response( $responseData,  200 );
            // OK ( 401 ) being the HTTP response code
        }
    }

    public function delete_get() {
        $fu_id = $this->uri->segment( 5 );
        $data = [
            'fu_is_deleted' => 'yes'
        ];
        $updateStatus = $this->tbl_generic_model->edit( 'functional_units', $data, array( 'fu_id' => $fu_id ) );
        $responseData = [
            'status' => 'success',
            'message' => 'Deleted successfully.',
            'data' => []
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function image_list_get() {
        $fu_id = $this->uri->segment( 5 );
        $data = $this->fu_model->getImages( $fu_id );
        $responseData = [
            'status' => 'success',
            'message' => 'Listing successfully.',
            'data' => $data
        ];
        // $retData = AUTHORIZATION::generateToken( $responseData );
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    public function image_upload_post() {
        $responseData = [];
        $fu_id = $this->post( 'fu_id' );
        $created_by = $this->post( 'created_by' );
        $is_completed = $this->post( 'is_completed' );
        // $fData = $_FILES;
        $fData = $this->do_upload( $fu_id );
        if ( $fData['error'] === '' ) {
            $this->updateDefaultImage( $fu_id );
            $addData['fu_id'] = $fu_id;
            $addData['fu_img_name'] = $fData['data']['file_name'];
            $addData['created_by'] = $created_by;
            $addData['is_default'] = '1';
            $addData['is_completed'] = $is_completed;
            $insertId = $this->tbl_generic_model->add( 'functional_unit_images', $addData );
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

    public function image_list_default_get() {
        $fu_id = $this->uri->segment( 5 );
        $fu_img_id = $this->uri->segment( 6 );
        $this->updateDefaultImage( $fu_id );
        $updateEventData['is_default'] = '1';
        $updateWhereData['fu_id'] = $fu_id;
        $updateWhereData['fu_img_id'] = $fu_img_id;
        $this->tbl_generic_model->edit( 'functional_unit_images', $updateEventData, $updateWhereData );
        $responseData = [
            'status' => 'success',
            'message' => 'Successfully updated default Image.',
            'data' => ''
        ];
        $this->response( $responseData,  200 );
        // OK ( 200 ) being the HTTP response code
    }

    private function updateDefaultImage( $fu_id = 0 ) {
        $updateEventData['is_default'] = '0';
        $updateWhereData['fu_id'] = $fu_id;
        $this->tbl_generic_model->edit( 'functional_unit_images', $updateEventData, $updateWhereData );
    }

    public function delete_image_get() {
        $fu_id = $this->uri->segment( 5 );
        $fu_img_id = $this->uri->segment( 6 );
        $responseData = [
            'status' => 'success',
            'message' => '',
            'data' => ''
        ];
        if ( $fu_id > 0 && $fu_img_id > 0 ) {
            $where['fu_id'] = $fu_id;
            $where['fu_img_id'] = $fu_img_id;
            $data = $this->tbl_generic_model->get( 'functional_unit_images', '*', $where );
            if ( count( $data ) > 0 ) {
                if ( $data[0]->fu_img_name !== '' ) {
                    $this->tbl_generic_model->unlinkImage( $this->imagePath . $data[0]->fu_img_name );
                    $this->tbl_generic_model->delete( 'functional_unit_images', $where );
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