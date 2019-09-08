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

        // $para      = $UserData[SYSAUTH];
        // $titulo    = 'Autenticación de la cuenta';
        // $mensaje   = "Bienvenido a la web, puedes autenticar la cuenta ingresado a :\n".base_url().'/autenticar' ;
        // $cabeceras = 'From: webmaster@example.com' . "\r\n" .
        //              'Reply-To: webmaster@example.com' . "\r\n" .
        //              'X-Mailer: PHP/' . phpversion();
                     
        //  mail($para, $titulo, $mensaje, $cabeceras);
        


        $this->load->model('Admin/role');
        $data['Rols']=$this->role->get_Rols();

        $this->form_validation->set_rules('Credential','Credential',"required|is_unique[user.".SYSAUTH."]",array(
            'is_unique'=>'Esta credencial ya ha sido tomada'

        ));
        $this->form_validation->set_rules('Password','Password','required|min_length[3]|max_length[255]');
        $this->form_validation->set_rules('PasswordConf','Password confirmation','required|min_length[3]|max_length[255]|matches[Password]');


        if ($this->form_validation->run()===TRUE)
        {
            if ($this->login->registerUser())
            {
                redirect('login');

            }

        }

        $this->load->LayoutView(array('Admin/login/register'=>null));

    }


    public function ConfirmEmail($TokenConfirm=false)
    {
        if ($TokenConfirm==false)
            show_404();

        if ($this->login->ConfirmEmail($TokenConfirm))
            echo "si";
        else
            echo "no";
        
        die();



    }


    public function forgotPassword()
    {


    }
    public function changePassword()
    {


    }

}


?>