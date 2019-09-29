<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model
{

    public $table = '';
    public $image_url;
    function __construct()
    {
        // parent::__construct();
        $this->table = 'event_master';
        $this->image_url = base_url('images/events/');
    }

    public function admin_list($postData = [])
    {
        $image_url = $this->image_url;
        $this->db->select('EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time');
        $this->db->select_min('EL.address', 'address');
        $this->db->select_min('EL.pin', 'pin');
        $this->db->select_min('C.name', 'c_name');
        $this->db->select_min('R.name', 'r_name');
        $this->db->select_min('CITY.name', 'city_name');
        $this->db->select("GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE);
        $this->db->select_min("CONCAT('" . $image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_location EL', 'EL.el_id = EM.el_id', 'LEFT');
        $this->db->join('countries C', 'C.id = EL.country_id', 'LEFT');
        $this->db->join('regions R', 'R.id = EL.region_id', 'LEFT');
        $this->db->join('cities CITY', 'CITY.id = EL.city_id', 'LEFT');
        $this->db->join('event_programs_rel_rel EPRR', 'EPRR.epr_id = EM.epr_id', 'LEFT');
        $this->db->join('programs PRO', 'PRO.program_id = EPRR.program_id AND PRO.is_deleted = "no"', 'LEFT');
        $this->db->join('event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT');
        $this->db->where('EM.event_is_deleted', 'no');
        $this->whereAndLimit($postData);
        $this->db->group_by('EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time');
        if ($postData['draw'] === 1) {
            $this->db->order_by('EM.event_id', 'DESC');
            $this->db->limit(10, $postData['start']);
        } else {
            $length = $postData['length'];
            if ($length === 2) {
                $length = 10;
            }
            $this->db->order_by($postData['columns'][$postData['order'][0]['column']]['data'], $postData['order'][0]['dir']);
            $this->db->limit($length, $postData['start']);
        }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function whereAndLimit($postData = array())
    {
        if ($postData['search']['value']) {
            $match = trim($postData['search']['value']);
            $whereLike = " (`EM`.`event_title` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `EM`.`event_start_date_time` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `EM`.`event_end_date_time` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `PRO`.`program_title` LIKE '%" . $match . "%' ESCAPE '!'";
            $this->db->where($whereLike);
        }
    }

    public function admin_list_filter_count($postData = [])
    {
        $this->db->select('EM.event_id');
        $this->db->from('event_master EM');
        $this->db->where('EM.event_is_deleted', 'no');
        $this->whereAndLimit($postData);
        return $this->db->count_all_results();
    }

    public function admin_list_count($postData = [])
    {
        $this->db->select('EM.event_id');
        $this->db->from('event_master EM');
        $this->db->where('EM.event_is_deleted', 'no');
        return $this->db->count_all_results();
    }

    public function getSingle($event_id = 0)
    {
        $this->db->select('EM.*,EL.*, EPRR.program_id');
        $this->db->from('event_master EM');
        $this->db->join('event_location EL', 'EL.event_id = EM.event_id', 'LEFT');
        $this->db->join('event_programs_rel_rel EPRR', 'EPRR.epr_id = EM.epr_id', 'LEFT');
        $this->db->where('EM.event_is_deleted', 'no');
        $this->db->where('EM.event_id', $event_id);
        return $this->db->get()->result();
    }

    public function getImages($event_id = 0)
    {
        $this->db->select('EI.*');
        $this->db->select("CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) image_path ", false);
        $this->db->from('event_images EI');
        $this->db->where('EI.event_id', $event_id);
        return $this->db->get()->result();
    }

    public function getDataForHome()
    {
        $this->db->select('EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time');
        $this->db->select_min('EL.address', 'address');
        $this->db->select_min('EL.pin', 'pin');
        $this->db->select_min('C.name', 'c_name');
        $this->db->select_min('R.name', 'r_name');
        $this->db->select_min('CITY.name', 'city_name');
        $this->db->select("GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE);
        $this->db->select_min("CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_location EL', 'EL.el_id = EM.el_id', 'LEFT');
        $this->db->join('countries C', 'C.id = EL.country_id', 'LEFT');
        $this->db->join('regions R', 'R.id = EL.region_id', 'LEFT');
        $this->db->join('cities CITY', 'CITY.id = EL.city_id', 'LEFT');
        $this->db->join('event_programs_rel_rel EPRR', 'EPRR.epr_id = EM.epr_id', 'LEFT');
        $this->db->join('programs PRO', 'PRO.program_id = EPRR.program_id AND PRO.is_deleted = "no"', 'LEFT');
        $this->db->join('event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT');
        $this->db->where('EM.event_is_deleted', 'no');
        $this->db->where('EM.event_start_date_time >= ', date("Y-m-d H:i:s"));
        // $this->db->where_or('EM.event_end_date_time <= ', date("Y-m-d H:i:s"));
        $this->db->group_by('EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time');
        $this->db->order_by('EM.event_start_date_time', 'ASC');
        $this->db->limit(3);
        return $this->db->get()->result();
    }
    public function getEventForEvent()
    {
        $this->db->select('EM.*');
        $this->db->select_min('EL.address', 'address');
        $this->db->select_min('EL.pin', 'pin');
        $this->db->select_min('C.name', 'c_name');
        $this->db->select_min('R.name', 'r_name');
        $this->db->select_min('CITY.name', 'city_name');
        $this->db->select("GROUP_CONCAT(CONCAT('<p>',PRO.program_title,'</p>') SEPARATOR ' ') program_title", FALSE);
        $this->db->select_min("CONCAT('" . $this->image_url . "',IF(EI.ei_image_name!='',EI.ei_image_name,'no-image.png')) ", 'image_path');
        $this->db->from('event_master EM');
        $this->db->join('event_location EL', 'EL.el_id = EM.el_id', 'LEFT');
        $this->db->join('countries C', 'C.id = EL.country_id', 'LEFT');
        $this->db->join('regions R', 'R.id = EL.region_id', 'LEFT');
        $this->db->join('cities CITY', 'CITY.id = EL.city_id', 'LEFT');
        $this->db->join('event_programs_rel_rel EPRR', 'EPRR.epr_id = EM.epr_id', 'LEFT');
        $this->db->join('programs PRO', 'PRO.program_id = EPRR.program_id AND PRO.is_deleted = "no"', 'LEFT');
        $this->db->join('event_images EI', 'EI.event_id=EM.event_id AND EI.is_default="1"', 'LEFT');
        $this->db->where('EM.event_is_deleted', 'no');
        // $this->db->where('EM.event_start_date_time >= ', date("Y-m-d H:i:s"));
        // $this->db->where_or('EM.event_end_date_time <= ', date("Y-m-d H:i:s"));
        $this->db->group_by('EM.event_id,EM.event_title,EM.event_start_date_time,EM.event_end_date_time');
        $this->db->order_by('EM.event_start_date_time', 'DESC');
        // $this->db->limit(3);
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }
}
