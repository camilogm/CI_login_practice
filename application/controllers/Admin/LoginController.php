<?php

defined('BASEPATH') or exit('No se encontrò');
defined('SYSAUTH') or exit('');


class LoginController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/login');
        $this->login->verifyToken('true');
        $this->load->library('form_validation');

    }

    public function index()
    {
        $this->form_validation->set_rules('Credential','Credential','required');
        $this->form_validation->set_rules('Password','Password','required');
        
        

        if ($this->form_validation->run()===TRUE)
        {
            if($this->login->CheckAuth())
            {
                redirect('');

            }
        }

        $this->load->LayoutView(array('Admin/login/index'=>null));
    }

    public function register()
    {

        $this->load->model('Admin/role');
        $data['Rols']=$this->role->get_Rols();

        $this->form_validation->set_rules('Credential','Credential',"required|is_unique[user.".SYSAUTH."]",array(
            'is_unique'=>'Esta credencial ya ha sido tomada'

        ));
        $this->form_validation->set_rules('Password','Password','required|min_length[3]|max_length[255]');
        $this->form_validation->set_rules('PasswordConf','Password confirmation','required|min_length[3]|max_length[255]|matches[Password]');
        $this->form_validation->set_rules('Role','Role','required');


        if ($this->form_validation->run()===TRUE)
        {
            if ($this->login->registerUser())
            {
                redirect('login');

            }

        }

        $this->load->LayoutView(array('Admin/login/register'=>null));

    }

    public function forgotPassword()
    {


    }
    public function changePassword()
    {


    }

}


?>