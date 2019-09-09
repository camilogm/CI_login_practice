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
            $data['Message']=false;

        if ($this->login->ConfirmEmail($TokenConfirm))
            $data['Message']=true;
        else
            $data['Message']=false;
        
        
        $this->load->LayoutView(array('Admin/login/ConfirmEmail'=>$data));
    }
    public function AskConfirmEmail()
    {
        $this->form_validation->set_rules('Credential','Credential','required');
        
        $data['Message']='null';
        
        if ($this->form_validation->run()===TRUE)
        {
            $data['Message']=$this->login->sendConfirmEmail();         
        }
        $this->load->LayoutView(array('Admin/login/AskConfirmEmail'=>$data));
    }

    public function forgotPassword()
    {
        $this->form_validation->set_rules('Credential','Credential','required');        
        $data['Message']='null';        
        if ($this->form_validation->run()===TRUE)
        {
            $data['Message']=$this->login->sendRestartPassword();         
        }
        $this->load->LayoutView(array('Admin/login/AskPassword'=>$data));
    }

    public function changePassword($Token=false)
    {
        $data['Message']='null';
        if ($Token==false)
            $data['Message']='false';
        
       
        $data['Token']=$Token;

        $this->form_validation->set_rules('Password','Password','required');
        $this->form_validation->set_rules('PasswordConf','PasswordConf','required|matches[Password]');
        
        $aux=false;
        if ($this->form_validation->run()===true )
        {
          

            $data['Message']=$this->login->resetPassword($Token);
            $aux=true;
            
        }
        
        if (!$this->login->confirmTokenPassword($Token) && $aux==false)
            $data['Message']='false';

      

        $this->load->LayoutView(array('Admin/login/ChangePassword'=>$data));

        

    }


}


?>