<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tbl_generic_model extends CI_Model
{

    function __construct()
    {
        // parent::__construct();
    }


    /**
     * This is general select from any table
     */
    function get($table = '', $fields = '*', $where = array(), $orderby = array(), $perpage = 0, $start = 0, $one = false, $array = false)
    {
        $this->db->select($fields);
        $this->db->from($table);

        if ($perpage > 0 && $start > 0) {
            $this->db->limit($perpage, $start);
        }

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($orderby) > 0) {
            $orderby_str = '';
            foreach ($orderby as $key => $value) {
                $orderby_str .= $key . " " . $value . ",";
            }
            $orderby_str = substr($orderby_str, 0, -1);
            $this->db->order_by($orderby_str);
        }

        $query = $this->db->get();

        if (!$one) {
            if ($array === true) {
                $result = $query->result_array();
            } else {
                $result = $query->result();
            }
        } else {
            $result = $query->row();
        }
        return $result;
    }

    /**
     * Add data to a table
     */
    function add($table = '', $data = array())
    {
        $this->db->set($data);
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') return $this->db->insert_id();
        return FALSE;
    }

    /**
     * Add batch insert toa a table
     */
    function add_batch($table = '', $data = array())
    {
        $this->db->insert_batch($table, $data);
        return true;
    }

    /**
     * Update to a tbale
     */
    function edit($table = '', $data = array(), $where = array())
    {

        $this->db->set($data);
        $this->db->update($table, $data, $where);
        // echo $this->db->last_query();
        // exit();
        if ($this->db->affected_rows() >= 0) return TRUE;

        return FALSE;
    }

    /**
     * Delete data from a table
     */
    function delete($table = '', $where = array())
    {
        $this->db->delete($table, $where);
        if ($this->db->affected_rows() == '1') return TRUE;
        return FALSE;
    }

    /**
     * Count data from a table
     */
    function count($table = '')
    {
        return $this->db->count_all($table);
    }

    /**
     * Count with where from a table
     */
    function countWhere($table = '', $where = array())
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->from($table);
        return $this->db->count_all_results();
    }

    /**
     * Query execute
     */
    function sqlQuery($queryStatement, $result_type = 'object')
    {
        $q = $this->db->query($queryStatement);
        // echo $this->db->last_query();
        if (is_object($q)) {
            if ($result_type == 'object')
                return $q->result();
            else
                return $q->result_array();
        } else {
            return $q;
        }
    }


    /**
     * 
     */
    function callProcedure($procedure)
    {
        $result = @$this->db->conn_id->query($procedure);

        while ($this->db->conn_id->more_results() && $this->db->conn_id->next_result()) {
            //free each result.
            $not_used_result = $this->db->conn_id->use_result();

            if ($not_used_result instanceof mysqli_result) {
                $not_used_result->free();
            }
        }

        return $result;
    }

    /*Get loss details*/
    public function callProcedureListing($procedure)
    {
        $query = $this->db->query($procedure);
        $data = $query->result();
        $this->callAfterProcedure($query);
        return $data;
    }

    /*get common function*/
    public function callAfterProcedure($query)
    {
        $query->next_result();
        $query->free_result();
    }

    public function truncate($table = '')
    {
        $this->db->from($table);
        $this->db->truncate();
        return true;
    }

    public function sendEmail($to = '', $subject = '', $body = '', $cc = array(), $bcc = array())
    {
        $this->load->library('email');
        /*$config['protocol'] = 'sendmail';*/
        $config['protocol'] = 'SMTP';
        $config['smtp_host'] = 'mail.parrotdipankar.com';
        $config['smtp_user'] = 'info@parrotdipankar.com';
        $config['smtp_pass'] = 'TOshJemkebmyWu9';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';

        $this->email->initialize($config);
        $this->email->from(SUPPORTEMAIL, SITENAME);
        $this->email->to($to);
        if (count($cc) > 0) {
            $this->email->cc($cc);
        }
        if (count($bcc) > 0) {
            $this->email->bcc($bcc);
        }
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();
    }

    public function getCountryList()
    {
        $this->db->select('*');
        $this->db->from('countries');
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getStateList($country_id = 0)
    {
        $this->db->select('*');
        $this->db->from('states');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getCityList($state_id = 0)
    {
        $this->db->select('*');
        $this->db->from('cities');
        $this->db->where('state_id', $state_id);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }
}
