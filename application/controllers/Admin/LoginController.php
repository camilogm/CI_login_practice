<?php

defined('BASEPATH') or exit('No se encontrò');
defined('SYSAUTH') or exit('');

require_once  'vendorGoogle/autoload.php';
require_once 'vendorGoogle\google\apiclient-services\src\Google\Service\Oauth2.php';



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
        $data['GURL']=$this->GetGoogleURL();
        

        if ($this->form_validation->run()===TRUE)
        {
            if($this->login->CheckAuth())
            {
                redirect('');

            }
        }
        $this->load->LayoutView(array('Admin/login/index'=>$data));
    }

    public function register()
    {
        $this->load->model('Admin/role');
        $data['Rols']=$this->role->get_Rols();

        $this->form_validation->set_rules('Credential','Credential',"required|is_unique[user.".SYSAUTH."]",array(
            'is_unique'=>'Esta credencial ya ha sido tomada'

        ));

        $data['GURL']=$this->GetGoogleURL();


        $this->form_validation->set_rules('Password','Password','required|min_length[3]|max_length[255]');
        $this->form_validation->set_rules('PasswordConf','Password confirmation','required|min_length[3]|max_length[255]|matches[Password]');


        if ($this->form_validation->run()===TRUE)
        {
            if ($this->login->registerUser())
            {
                redirect('login');
            }

        }
        $this->load->LayoutView(array('Admin/login/register'=>$data));
    }
    
    private function GetGoogleURL()
    {
        $g_client = new Google_Client();
        $g_client->setClientId("");
        $g_client->setClientSecret("");
        $g_client->setRedirectUri(base_url()."login/signinGoogle");
        $g_client->setScopes(array("email",'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'));
        //Step 2 : Create the url
        $auth_url = $g_client->createAuthUrl();
        return $auth_url;

    }


    
    public function signIn_Google()
    {
        
        $g_client = new Google_Client();
        $g_client->setClientId("710306109033-jpf55i7e7jcgfue2g60dibnfb544495i.apps.googleusercontent.com");
        $g_client->setClientSecret("n1HcHlqezs3sHkZI4EM4Eq3r");

       
        $g_client->setRedirectUri(base_url()."login/signinGoogle");
        $g_client->setScopes(array("email",'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'));
        //Step 2 : Create the url
        $auth_url = $g_client->createAuthUrl();
        //Step 3 : Get the authorization  code
        $code = isset($_GET['code']) ? $_GET['code'] : NULL;
        //Step 4: Get access token

        $plus = new Google_Service_Oauth2($g_client);

        if(isset($code)) {
            try {
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                $g_client->setAccessToken($token);
            }catch (Exception $e){
                echo $e->getMessage();
            }
            try {
                $pay_load = $g_client->verifyIdToken();
            }catch (Exception $e) {
                echo $e->getMessage();
            }
        } else{
            $pay_load = null;
        }
        if(isset($pay_load))
        {
            $userinfo = $plus->userinfo->get();
           
            
            if($this->login->signIn_Google($userinfo))
                redirect(base_url());
            else 
            {

            }
            

        }



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