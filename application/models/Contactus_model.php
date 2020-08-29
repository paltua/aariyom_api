<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contactus_model extends CI_Model
{

    public $table = '';
    public $image_url;
    function __construct()
    {
        // parent::__construct();
        $this->table = 'contact_us';
        $this->image_url = base_url('images/events/');
    }

    public function admin_list($postData = [])
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->whereAndLimit($postData);
        if ($postData['draw'] === 1) {
            $this->db->order_by('con_id', 'DESC');
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
            $whereLike = " (`name` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `email` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `mobile` LIKE '%" . $match . "%' ESCAPE '!' 
            OR `desccription` LIKE '%" . $match . "%' ESCAPE '!')";
            $this->db->where($whereLike);
        }
    }

    public function admin_list_filter_count($postData = [])
    {
        $this->db->select('con_id');
        $this->db->from($this->table);
        $this->whereAndLimit($postData);
        return $this->db->count_all_results();
    }

    public function admin_list_count($postData = [])
    {
        $this->db->select('con_id');
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function getSingle($con_id = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('con_id', $con_id);
        return $this->db->get()->result();
    }
}
