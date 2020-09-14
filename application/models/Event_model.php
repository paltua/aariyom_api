<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Event_model extends CI_Model {

    public $table = '';
    public $image_url;

    function __construct() {
        // parent::__construct();
        $this->table = 'event_master';
        $this->image_url = base_url( IMAGE_PATH__EVENT );
    }

    public function admin_list( $postData = [] ) {
        $this->db->select( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        $this->whereAndLimit( $postData );
        $this->db->group_by( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time' );
        if ( $postData['draw'] === 1 ) {
            $this->db->order_by( 'EM.event_id', 'DESC' );
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
            $whereLike = " (`EML`.`event_title` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `EML`.`event_start_date_time` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `EML`.`event_end_date_time` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `PRO`.`program_title` LIKE '%" . $match . "%' ESCAPE '!'";
            $this->db->where( $whereLike );
        }
    }

    public function admin_list_filter_count( $postData = [] ) {
        $this->db->select( 'EM.event_id' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        $this->whereAndLimit( $postData );
        return $this->db->count_all_results();
    }

    public function admin_list_count( $postData = [] ) {
        $this->db->select( 'EM.event_id' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        return $this->db->count_all_results();
    }

    public function getSingle( $event_id = 0 ) {
        $this->db->select( 'EML.*,EL.*, EPR.program_id' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->where( 'EML.event_status != ', 'deleted' );
        $this->db->where( 'EM.event_id', $event_id );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getSingleForDelete( $event_id = 0 ) {
        $this->db->select( 'EML.*' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->where( 'EM.event_id', $event_id );
        return $this->db->get()->result_array();
    }

    public function getImages( $event_id = 0 ) {
        $this->db->select( 'EI.*' );
        $this->db->select( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) image_path ", false );
        $this->db->select( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_images EI' );
        $this->db->where( 'EI.event_id', $event_id );
        $this->db->order_by( 'EI.is_default', 'ASC' );
        return $this->db->get()->result();
    }

    public function getDataForHome() {
        $this->db->select( 'EM.event_id,EML.event_title,EML.event_short_desc,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        // $this->db->where( 'EM.event_start_date_time >= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->where_or( 'EM.event_end_date_time <= ', date( 'Y-m-d H:i:s' ) );
        $this->db->group_by( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url' );
        $this->db->order_by( 'EML.event_start_date_time', 'ASC' );
        $this->db->limit( 10 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getDataForHomeSlider() {
        $this->db->select( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url,EML.event_short_desc' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        // $this->db->where( 'EM.event_start_date_time >= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->where_or( 'EM.event_end_date_time <= ', date( 'Y-m-d H:i:s' ) );
        $this->db->group_by( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url' );
        $this->db->order_by( 'EML.event_start_date_time', 'ASC' );
        $this->db->limit( 5 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getDataForEvent() {
        $this->db->select( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url,EML.event_short_desc' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        // $this->db->where( 'EM.event_start_date_time >= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->where_or( 'EM.event_end_date_time <= ', date( 'Y-m-d H:i:s' ) );
        $this->db->group_by( 'EM.event_id,EML.event_title,EML.event_start_date_time,EML.event_end_date_time,EML.event_title_url' );
        $this->db->order_by( 'EML.event_start_date_time', 'DESC' );
        // $this->db->limit( 3 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getEventDetails( $event_id = 0 ) {
        $this->db->select( 'EML.*' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        // $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        // $this->db->join( 'event_location EL', 'EL.el_id = EM.el_id', 'LEFT' );
        // $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        // $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        // $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        // $this->db->join( 'event_programs_rel_rel EPR', 'EPR.epr_id = EM.epr_id', 'LEFT' );
        // $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        // $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        // $this->db->where( 'EM.event_status', 'no' );

        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        $this->db->join( 'event_location EL', 'EL.eml_id = EM.eml_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel EPR', 'EPR.eml_id = EML.eml_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        $this->db->where( 'EM.event_id', $event_id );
        // $this->db->group_by( 'EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time' );
        // $this->db->order_by( 'EM.event_start_date_time', 'DESC' );
        // $this->db->limit( 3 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getUpcomingEvent() {
        $this->db->select( 'EM.*, EML.*' );
        // $this->db->select_min( 'EL.address', 'address' );
        // $this->db->select_min( 'EL.pin', 'pin' );
        // $this->db->select_min( 'C.name', 'c_name' );
        // $this->db->select_min( 'R.name', 'r_name' );
        // $this->db->select_min( 'CITY.name', 'city_name' );
        // $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        // $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_master_log EML', 'EML.eml_id=EM.eml_id', 'INNER' );
        // $this->db->join( 'event_location EL', 'EL.el_id = EM.el_id', 'LEFT' );
        // $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        // $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        // $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        // $this->db->join( 'event_programs_rel_rel EPR', 'EPR.epr_id = EM.epr_id', 'LEFT' );
        // $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        // $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EML.event_status !=', 'deleted' );
        // $this->db->where( 'EM.event_start_date_time >= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->where_or( 'EM.event_end_date_time <= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->group_by( 'EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time' );
        $this->db->order_by( 'EML.event_start_date_time', 'ASC' );
        $this->db->limit( 5 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getArchive() {
        $this->db->select( 'EM.*' );
        $this->db->select_min( 'EL.address', 'address' );
        $this->db->select_min( 'EL.pin', 'pin' );
        $this->db->select_min( 'C.name', 'c_name' );
        $this->db->select_min( 'R.name', 'r_name' );
        $this->db->select_min( 'CITY.name', 'city_name' );
        $this->db->select( "GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE );
        $this->db->select_min( "CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path' );
        $this->db->select_min( "CONCAT('" . $this->image_url . "thumb/',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path_thumb' );
        $this->db->from( 'event_master EM' );
        $this->db->join( 'event_location EL', 'EL.el_id = EM.el_id', 'LEFT' );
        $this->db->join( 'countries C', 'C.id = EL.country_id', 'LEFT' );
        $this->db->join( 'regions R', 'R.id = EL.region_id', 'LEFT' );
        $this->db->join( 'cities CITY', 'CITY.id = EL.city_id', 'LEFT' );
        $this->db->join( 'event_programs_rel_rel EPR', 'EPR.epr_id = EM.epr_id', 'LEFT' );
        $this->db->join( 'programs PRO', 'PRO.program_id = EPR.program_id AND PRO.is_deleted = "no"', 'LEFT' );
        $this->db->join( 'event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT' );
        $this->db->where( 'EM.event_status', 'no' );
        // $this->db->where( 'EM.event_start_date_time >= ', date( 'Y-m-d H:i:s' ) );
        // $this->db->where_or( 'EM.event_end_date_time <= ', date( 'Y-m-d H:i:s' ) );
        $this->db->group_by( 'EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time' );
        $this->db->order_by( 'EM.event_start_date_time', 'DESC' );
        // $this->db->limit( 3 );
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }
}