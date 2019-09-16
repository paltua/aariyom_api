<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Programme_model extends CI_Model
{

    public $table = '';
    function __construct()
    {
        // parent::__construct();
        $this->table = 'programs';
    }

    public function admin_list($postData = [])
    {
        $this->db->select('PROG.*, UMD.user_name');
        $this->db->from('programs PROG');
        $this->db->join('user_master UM', 'UM.user_id=PROG.created_by', 'inner');
        $this->db->join('user_master_details UMD', 'UMD.user_id=UM.user_id', 'inner');
        $this->db->where('PROG.is_deleted', 'no');
        $this->whereAndLimit($postData);
        if ($postData['draw'] === 0) {
            $this->db->order_by('PROG.program_id', 'DESC');
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
            $whereLike = " (`PROG`.`program_title` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `PROG`.`program_desc` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `UMD`.`user_name` LIKE '%" . $match . "%' ESCAPE '!')";
            $this->db->where($whereLike);
        }
    }

    public function admin_list_filter_count($postData = [])
    {
        $this->db->select('PROG.*, UMD.user_name');
        $this->db->from('programs PROG');
        $this->db->join('user_master UM', 'UM.user_id=PROG.created_by', 'inner');
        $this->db->join('user_master_details UMD', 'UMD.user_id=UM.user_id', 'inner');
        $this->db->where('PROG.is_deleted', 'no');
        $this->whereAndLimit($postData);
        return $this->db->count_all_results();
    }

    public function admin_list_count($postData = [])
    {
        $this->db->select('PROG.*, UMD.user_name');
        $this->db->from('programs PROG');
        $this->db->join('user_master UM', 'UM.user_id=PROG.created_by', 'inner');
        $this->db->join('user_master_details UMD', 'UMD.user_id=UM.user_id', 'inner');
        $this->db->where('PROG.is_deleted', 'no');
        return $this->db->count_all_results();
    }
}
