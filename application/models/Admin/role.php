<?php

defined('BASEPATH') or exit('No se encontró');


class role extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_Rols($Id=false)
    {
        if ($Id==false)
        {
            $query=$this->db->get('role');
            return $query->result_array();

        }    

    }

    



}


?>