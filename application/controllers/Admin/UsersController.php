<?php


class UsersController extends CI_Controller
{
    public $Log;

    public function __construct()
    {

        parent::__construct();      
        $this->load->model('Admin/login');


        
    }

    public function index()
    {
       
        $this->load->LayoutView();
    }

    public function create()
    {

        echo "ahuevo";
    }




}


?>