<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Fu_model extends CI_Model {

    public $table = '';
    public $image_url;

    function __construct() {
        // parent::__construct();
        $this->table = 'functional_units';
        $this->image_url = base_url( IMAGE_PATH__FU );
    }

    public function admin_list( $postData = [] ) {
        $this->db->select( "FU.*, CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url . "thumb/',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path_thumb", false );
        $this->db->from( 'functional_units FU' );
        $this->db->join( 'functional_unit_images FUIMG', 'FUIMG.fu_id=FU.fu_id AND FUIMG.is_default = "1"', 'LEFT' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->whereAndLimit( $postData );
        if ( $postData['draw'] === 1 ) {
            $this->db->order_by( 'FU.fu_id', 'DESC' );
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
            $whereLike = " (`FU`.`fu_title` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `FU`.`fu_desc` LIKE '%" . $match . "%' ESCAPE '!' ";
            $this->db->where( $whereLike );
        }
    }

    public function admin_list_filter_count( $postData = [] ) {
        $this->db->select( 'FU.fu_id' );
        $this->db->from( 'functional_units FU' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->whereAndLimit( $postData );
        return $this->db->count_all_results();
    }

    public function admin_list_count( $postData = [] ) {
        $this->db->select( 'FU.fu_id' );
        $this->db->from( 'functional_units FU' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        return $this->db->count_all_results();
    }

    public function getSingle( $fu_id = 0 ) {
        $this->db->select( 'FU.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url . "thumb/',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path_thumb", false );
        $this->db->from( 'functional_units FU' );
        $this->db->join( 'functional_unit_images FUIMG', 'FUIMG.fu_id=FU.fu_id AND FUIMG.is_default = "1"', 'LEFT' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->db->where( 'FU.fu_id', $fu_id );
        return $this->db->get()->result();
    }

    public function getDataForHome() {
        $this->db->select( 'FU.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url . "thumb/',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path_thumb", false );
        $this->db->from( 'functional_units FU' );
        $this->db->join( 'functional_unit_images FUIMG', 'FUIMG.fu_id=FU.fu_id AND FUIMG.is_default = "1"', 'LEFT' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->db->order_by( 'FU.fu_id', 'DESC' );
        $this->db->limit( 20 );
        return $this->db->get()->result();
    }

    public function getSingleFrontEnd( $fu_title_url = '' ) {
        $this->db->select( 'FU.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url . "thumb/',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path_thumb", false );
        $this->db->from( 'functional_units FU' );
        $this->db->join( 'functional_unit_images FUIMG', 'FUIMG.fu_id=FU.fu_id ', 'LEFT' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->db->where( 'FU.fu_title_url', $fu_title_url );
        $this->db->order_by( 'FUIMG.is_default', 'ASC' );
        return $this->db->get()->result();
    }

    public function getOthersFu( $fu_id = 0 ) {
        $this->db->select( 'FU.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path" );
        $this->db->select( " CONCAT('" . $this->image_url . "thumb/',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path_thumb", false );
        $this->db->from( 'functional_units FU' );
        $this->db->join( 'functional_unit_images FUIMG', 'FUIMG.fu_id=FU.fu_id AND FUIMG.is_default = "1"', 'LEFT' );
        $this->db->where( 'FU.fu_is_deleted', 'no' );
        $this->db->where( 'FU.fu_id != ', $fu_id );
        $this->db->order_by( 'FU.fu_id', 'DESC' );
        // $this->db->limit( 20 );
        return $this->db->get()->result();
    }

    public function getImages( $fu_id = 0 ) {
        $this->db->select( 'FUIMG.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(FUIMG.fu_img_name!='',FUIMG.fu_img_name,'no-image.png')) image_path ", false );
        $this->db->select( 'CONCAT("' . $this->image_url . 'thumb/",IF(FUIMG.fu_img_name!="",FUIMG.fu_img_name,"no-image.png")) image_path_thumb', false );
        $this->db->from( 'functional_unit_images FUIMG' );
        $this->db->where( 'FUIMG.fu_id', $fu_id );
        $this->db->order_by( 'FUIMG.is_default', 'ASC' );
        return $this->db->get()->result();
    }
}