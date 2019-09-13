<?php

defined('BASEPATH') or exit('No se encontrò');
defined('SYSAUTH') or exit('');

require_once  'vendorGoogle/autoload.php';
require_once 'vendorGoogle\google\apiclient-services\src\Google\Service\Oauth2.php';
require_once 'vendorFacebook\FacebookClass.php';



class LoginController extends CI_Controller
{
   
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/login');
        $this->load->model('Admin/Authcheck');
        
        $this->load->library('form_validation');        

    }

    public function index()
    {
        $this->Authcheck->verifyToken('true');

        $this->form_validation->set_rules('Credential','Credential','required');
        $this->form_validation->set_rules('Password','Password','required');
        


        $FBClass=new FacebookClass();
        $data['GURL']=$this->GetGoogleURL();
        $data['FURL']=$FBClass->GetUrlFacebook(base_url().'login/callbackFacebook');

        if ($this->form_validation->run()===TRUE)
        {
            if($this->login->CheckAuth())
            {
                redirect('');

            }
        }
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/index'=>$data));
    }

    public function logout()
    {
        
        if ($this->login->logout())
            redirect(base_url().'login');

    }



    public function register()
    {

        
        $this->Authcheck->verifyToken('true');

        $this->load->model('Admin/role');
        $data['Rols']=$this->role->get_Rols();

        $this->form_validation->set_rules('Credential','Credential',"required|is_unique[user.".SYSAUTH."]",array(
            'is_unique'=>'Esta credencial ya ha sido tomada'

        ));

        $FBClass=new FacebookClass();
        $data['GURL']=$this->GetGoogleURL();        
        $data['FURL']=$FBClass->GetUrlFacebook(base_url().'login/callbackFacebook');

        $this->form_validation->set_rules('Password','Password','required|min_length[3]|max_length[255]');
        $this->form_validation->set_rules('PasswordConf','Password confirmation','required|min_length[3]|max_length[255]|matches[Password]');


        if ($this->form_validation->run()===TRUE)
        {
            if ($this->login->registerUser())
            {
                redirect('login');
            }
        }
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/register'=>$data));
    }
    
    private function GetGoogleURL()
    {
        
        $this->Authcheck->verifyToken('true');

        $g_client = new Google_Client();
        $g_client->setClientId("710306109033-jpf55i7e7jcgfue2g60dibnfb544495i.apps.googleusercontent.com");
        $g_client->setClientSecret("n1HcHlqezs3sHkZI4EM4Eq3r");
        $g_client->setRedirectUri(base_url()."login/signinGoogle");
        $g_client->setScopes(array("email",'https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'));
        //Step 2 : Create the url
        $auth_url = $g_client->createAuthUrl();
        return $auth_url;

    }

    public function callback_Facebook()
    {
        
        $this->Authcheck->verifyToken('true');

        $f_client=new FacebookClass();        
        $UserData=$f_client->CallBack(base_url().'login/signInFacebook');
        
       
    }
    public function  signIn_Facebook()
    {
        
        $this->Authcheck->verifyToken('true');
        $UserData=$_SESSION['UserData'];
        session_unset('UserData');
        
        if($this->login->signIn_App($UserData,'Facebook'))
                redirect(base_url());
        else 
        {

        }
        
    }
    public function signIn_Google()
    {
        
        $this->Authcheck->verifyToken('true');
        $g_client = new Google_Client();
        $g_client->setClientId("");
        $g_client->setClientSecret("");

       
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
           
            
            if($this->login->signIn_App($userinfo,'gmail'))
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
        
        
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/ConfirmEmail'=>$data));
    }
    public function AskConfirmEmail()
    {
        $this->form_validation->set_rules('Credential','Credential','required');
        
        $data['Message']='null';
        
        if ($this->form_validation->run()===TRUE)
        {
            $data['Message']=$this->login->sendConfirmEmail();         
        }
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/AskConfirmEmail'=>$data));
    }

    public function forgotPassword()
    {
        
        $this->Authcheck->verifyToken('true');

        $this->form_validation->set_rules('Credential','Credential','required');        
        $data['Message']='null';        
        if ($this->form_validation->run()===TRUE)
        {
            $data['Message']=$this->login->sendRestartPassword();         
        }
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/AskPassword'=>$data));
    }

    public function changePassword($Token=false)
    {
        
        $this->Authcheck->verifyToken('true');
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

      

        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/ChangePassword'=>$data));
    }

    public function AskPermissions($Permission='')
    {
        $data['Permission']=$Permission;
        $this->Authcheck->verifyToken();
        
        $this->load->LayoutView($this->Authcheck->get_UserConnected(),array('Admin/login/AskPermissions'=>$data));
    }




}


?>