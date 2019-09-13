<?php

defined('BASEPATH') or exit('No se encontró');
defined('PATH');
defined('SYSAUTH') or exit('NAN');
defined('SYSPASS') or exit('NAN');
defined('DBTables') or exit('NAN');


// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;


// require 'PHPMailer\src\PHPMailer.php';
// require 'PHPMailer\src\Exception.php';
// require 'PHPMailer\src\SMTP.php';



class login extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
        session_start();
 
        
    }

    public function CheckAuth()
    {
        
        //Contiene la información ingresada a través del formulario
        $UserData=array(
             SYSAUTH=>$this->input->post('Credential'),
             SYSPASS=>$this->input->post('Password')
        );



        $this->db->where(SYSAUTH,$UserData[SYSAUTH]);
        $query=$this->db->get(DBTables['User']);
        //Contiene la información de la base de datos
        $UserQuery=$query->row_array();

        //Verifica que exista la fila o no hay usuario
        if ($UserQuery!=null)
        {
            //Desencripta la contraseña y la compara
            if (password_verify($UserData[SYSPASS],$UserQuery[SYSPASS]))
            {
                $this->setTokenUser($UserQuery['User_Id'],$this->randomToken());
                return true;
            }
        }
        else
        {

        }
        return false;
    }

    public function logout()
    {
        if (isset($_SESSION['MAUTH']))        
        {     
            $token=$_SESSION['MAUTH'];
            $this->db->where('token',$token);
            $this->db->delete(DBTables['tokenuser']);
            session_unset('MAUTH');
            
            return true;
        
        
        }
         else 
            return false;

       
    }

    //Estable el token de conexión al que estará enlazado el navegador
    private function setTokenUser($UserId,$Token)
    {
        $TokenEncode=password_hash($Token,PASSWORD_ARGON2I);
        $TokenData=array(
            'User_Id'=>$UserId,
            'Token'=>$TokenEncode
        );
        

        $this->db->insert(DBTables['tokenuser'],$TokenData);
        $_SESSION['MAUTH']=$TokenEncode;        
    }
  


    //registra usuario
    public function registerUser()
    {
    //setea los datos del usuario y los ingresa a la BD
    $UserData=array(
        SYSAUTH=>$this->input->post('Credential'),
        SYSPASS=>password_hash($this->input->post('Password'),PASSWORD_ARGON2I),
        'Role_Id'=>$this->input->post('Role')
    );

  


    $this->db->insert(DBTables['User'],$UserData);    
    $this->db->where(SYSAUTH,$UserData[SYSAUTH]);
    $User_Id=(($this->db->get(DBTables['User']))->row_array())['User_Id'];

    $ANIData=array(
        'User_Id'=>$User_Id,
        'Token'=>$this->randomToken(15)
    );
    $this->db->insert(DBTables['ANI'],$ANIData);
    
    $URLConfirmation=base_url().'login/verificarcuenta/'.$ANIData['Token'];
    $message = "¡Bienvenido a  LoginPhp!Puedes autenticar tu cuenta ingresando a\n$URLConfirmation";
    $subject='Autenticación de la cuenta';


    $this->SendEmail($UserData[SYSAUTH],$message,$subject);   
    
    return true;
    }

   

    public function SignIn_App($UserInfo=null,$App)
    {
        if ($UserInfo==null)
            return false;

        $this->db->where(SYSAUTH,$UserInfo['email']);
        $UserId=(($this->db->get(DBTables['User']))->row_array())['User_Id'];
    
        if ($UserId!=null)
        {
            $this->setTokenUser($UserId,$this->randomToken());
            redirect();

        }
            



        $pass=$this->randomToken(16);
        $UserData=array(
            SYSAUTH=>$UserInfo['email'],
            SYSPASS=>password_hash($pass,PASSWORD_ARGON2I),
            'ConfirmEmail'=>TRUE
        );

        $this->db->insert(DBTables['User'],$UserData);

        $this->db->where(SYSAUTH,$UserData[SYSAUTH]);
        $UserId=(($this->db->get(DBTables['User']))->row_array())['User_Id'];

        if ($UserId==null)
            return false;

        $message="¡Bienvenido a LoginPhp!<br>Se ha registrado tu cuenta usando $App";
        $message=$message."se estableció tu contraseña automáticamente como: ".$pass;
        $subject='Se ha creado tu cuenta';

        $this->SendEmail($UserData[SYSAUTH],$message,$subject);
        $this->setTokenUser($UserId,$this->randomToken());
        return true;

    }

    //cuando se solicita
    public function sendConfirmEmail()
    {
        $this->db->where(SYSAUTH,$this->input->post('Credential'));
        $UserData=($this->db->get(DBTables['User']))->row_array();

        if ($UserData['ConfirmEmail']==true)
            return 'ver';

          
        if ($UserData!=null)
        {
            $this->db->where('User_id',$UserData['User_Id']);
            $this->db->delete(DBTables['ANI']);

            $ANIData=array(
                'User_Id'=>$UserData['User_Id'],
                'Token'=>$this->randomToken(15)
            );

            $this->db->insert(DBTables['ANI'],$ANIData);

          $URLConfirmation=base_url().'login/verificarcuenta/'.$ANIData['Token'];
          $message = "¡Hola de nuevo!Puedes autenticar tu cuenta ingresando a\n$URLConfirmation";
          $subject='Autenticación de la cuenta';

 
          $this->SendEmail($UserData[SYSAUTH],$message,$subject);   

            return 'true';
        }
        else
            return 'false';
    }

    public function sendRestartPassword()
    {
        $this->db->where(SYSAUTH,$this->input->post('Credential'));
        $UserData=($this->db->get(DBTables['User']))->row_array();

        
        if ($UserData!=null)
        {
            $this->db->where('User_id',$UserData['User_Id']);
            $this->db->delete(DBTables['ANI']);


            $GToken=password_hash($this->randomToken(255),PASSWORD_ARGON2I);
            $Vec=explode('$argon2i$v=19$m=1024,t=2,p=2$',$GToken);   
            
            $Token=$Vec[1];            
            $Token=str_replace('/','-',$Token);
            $Token=str_replace('$','_',$Token);

        
            $ChangePasswordData=array(
                'User_Id'=>$UserData['User_Id'],
                'Token'=>$Token
            );

                   
            $this->db->insert(DBTables['ChangePassword'],$ChangePasswordData);

            $URLConfirmation=base_url().'login/cambiarpass/'.$ChangePasswordData['Token'];
            $message = "¡Hola de nuevo!Puedes cambiar la contraseña de  tu cuenta ingresando a  <br/> <a href='$URLConfirmation'>este link</a>";
            $subject='Restablecimiento de contraseña';   
            $this->SendEmail($UserData[SYSAUTH],$message,$subject);   

        
            return 'true';
        }
        else
            return 'false';
    }


    private function SendEmail($EmailToSend='',$message='',$subject='')
    {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => '', 
            'smtp_pass' => '', 
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
        );        
   
        
   
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('');
        $this->email->to($EmailToSend);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
               
        // $mail=new PHPMailer(); 
        // $mail->isSMTP();
        // $mail->SMTPAuth=true;
        // $mail->SMTPSecure='ssl';
        // $mail->Host='smtp.gmail.com';
        // $mail->Port='465';
        // $mail->isHTML();
        // $mail->Username='';
        // $mail->Password='';
        // $mail->SetFrom('no-replay@CC.org');
        // $mail->Subject='Autenticación de la cuenta';
        // $mail->Body=$message;
        // $mail->AddAddress($UserData[SYSAUTH]);
        // $mail->Send();
    }


    public function ConfirmEmail($TokenConfirmation=false)
    {
        $this->db->where('Token',$TokenConfirmation);
        $ANIData=($this->db->get(DBTables['ANI']))->row_array();

        if ($ANIData==null)
            return false;

        $this->db->where('User_Id',$ANIData['User_Id']);
        $UserData=($this->db->get(DBTables['User']))->row_array();
        
        if ($UserData==null)
            return false;

        $UserData['ConfirmEmail']=TRUE;
        $this->db->where('User_Id',$UserData['User_Id']);
        $this->db->update(DBTables['User'],$UserData);
        $this->db->where('Token',$TokenConfirmation);
        $this->db->delete(DBTables['ANI']);
        return true;
    }

    public function confirmTokenPassword($Token=false)
    {
        
       
        
        $this->db->where('Token',$Token);
        $TokenCP=($this->db->get(DBTables['ChangePassword']))->row_array();

      
        if ($TokenCP==NULL)
            return false;
        else
            return true;

    }

    public function resetPassword($Token=false)
    {
        if($Token==false)
            return 'false';

        $this->db->where('Token',$Token);
        $User_Id=(($this->db->get(DBTables['ChangePassword']))->row_array())['User_Id'];

       
        if ($User_Id==null)
            return 'false';
        

         
        $this->db->where('User_Id',$User_Id);
        $UserData=($this->db->get(DBTables['User']))->row_array();

        if ($UserData==null)
            return 'false';

        
        $UserData[SYSPASS]=password_hash($this->input->post('Password'),PASSWORD_ARGON2I);        
        $this->db->where('User_Id',$UserData['User_Id']);
        $this->db->update(DBTables['User'],$UserData);

        $this->db->where('User_Id',$UserData['User_Id']);
        $this->db->delete(DBTables['ChangePassword']);

        return 'true';        

    }








    private function randomToken($length = 10) { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 

}

?>