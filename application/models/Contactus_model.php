<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Contactus_model extends CI_Model {

    public $table = '';
    public $image_url;

    function __construct() {
        // parent::__construct();
        $this->table = 'contact_us';
        $this->image_url = base_url( 'images/events/' );
        $this->image_url_about_us = base_url( 'images/about-us/' );
    }

    public function admin_list( $postData = [] ) {
        $this->db->select( '*' );
        $this->db->from( $this->table );
        $this->whereAndLimit( $postData );
        if ( $postData['draw'] === 1 ) {
            $this->db->order_by( 'con_id', 'DESC' );
            $this->db->limit( 10, $postData['start'] );
        } else {
            $length = $postData['length'];
            if ( $length === 2 ) {
                $length = 10;
            }
            $this->db->order_by( $postData['columns'][$postData['order'][0]['column']]['data'], $postData['order'][0]['dir'] );
            $this->db->limit( $length, $postData['start'] );
        }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function whereAndLimit( $postData = array() ) {
        if ( $postData['search']['value'] ) {
            $match = trim( $postData['search']['value'] );
            $whereLike = " (`name` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `email` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `mobile` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `desccription` LIKE '%" . $match . "%' ESCAPE '!')";
            $this->db->where( $whereLike );
        }
    }

    public function admin_list_filter_count( $postData = [] ) {
        $this->db->select( 'con_id' );
        $this->db->from( $this->table );
        $this->whereAndLimit( $postData );
        return $this->db->count_all_results();
    }

    public function admin_list_count( $postData = [] ) {
        $this->db->select( 'con_id' );
        $this->db->from( $this->table );
        return $this->db->count_all_results();
    }

    public function getSingle( $con_id = 0 ) {
        $this->db->select( '*' );
        $this->db->from( $this->table );
        $this->db->where( 'con_id', $con_id );
        return $this->db->get()->result();
    }

    public function admin_about_us_list( $postData = [] ) {
        $this->db->select( '*' );
        $this->db->select( "CONCAT('" . $this->image_url_about_us . "',IF(type='image',path,'')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url_about_us . "thumb/',IF(type='image',path,'')) image_path_thumb", false );
        $this->db->from( 'settings_midea_about_us' );
        $this->whereAndLimitAboutUs( $postData );
        $this->db->order_by( 'id', 'DESC' );
        // if ( $postData['draw'] === 1 ) {
        //     $this->db->order_by( 'id', 'DESC' );
        //     $this->db->limit( 10, $postData['start'] );
        // } else {
        //     $length = $postData['length'];
        //     if ( $length === 2 ) {
        //         $length = 10;
        //     }
        //     $this->db->order_by( $postData['columns'][$postData['order'][0]['column']]['data'], $postData['order'][0]['dir'] );
        //     $this->db->limit( $length, $postData['start'] );
        // }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function whereAndLimitAboutUs( $postData = array() ) {
        $whereLike = " status = 'active' ";
        $this->db->where( $whereLike );
        // if ( $postData['search']['value'] ) {
        //     $match = trim( $postData['search']['value'] );
        //     $whereLike = " status = 'active' ";
        //     $this->db->where( $whereLike );
        // }
    }

    public function admin_about_us_list_filter_count( $postData = [] ) {
        $this->db->select( 'id' );
        $this->db->from( 'settings_midea_about_us' );
        $this->whereAndLimitAboutUs( $postData );
        return $this->db->count_all_results();
    }

    public function admin_about_us_list_count( $postData = [] ) {
        $this->db->select( 'id' );
        $this->db->from( 'settings_midea_about_us' );
        return $this->db->count_all_results();
    }

    public function get_settings_about_us_data_home( $page = '' ) {
        $this->db->select( '*, IF(type="image",CONCAT("' . $this->image_url_about_us . '",IF(path!="image",path,"")),"") image_path' );
        $this->db->from( 'settings_midea_about_us' );
        $this->db->where( 'status', 'active' );
        $this->db->where( 'page', $page );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function get_settings_about_us_data_home_for ( $is_for = '' ) {
        $this->db->select( '*, IF(type="image",CONCAT("' . $this->image_url_about_us . '",IF(path!="image",path,"")),"") image_path' );
        $this->db->from( 'settings_midea_about_us' );
        $this->db->where( 'status', 'active' );
        $this->db->where( 'is_for', $is_for );
        $this->db->limit( 1 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function update_about_us_img_youtube_settings( $id = 0, $action_val = '' ) {
        $sql = "UPDATE settings_midea_about_us SET 
            `is_for` = IF(`id` = " . $id . ", '" . $action_val . "', IF(is_for != '" . $action_val . "',`is_for`,''))";
        return $this->db->query( $sql );
    }
}