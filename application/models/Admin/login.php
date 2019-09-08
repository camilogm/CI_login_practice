<?php

defined('BASEPATH') or exit('No se encontró');
defined('PATH');
defined('SYSAUTH') or exit('NAN');
defined('SYSPASS') or exit('NAN');
defined('DBTables') or exit('NAN');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer\src\PHPMailer.php';
require 'PHPMailer\src\Exception.php';
require 'PHPMailer\src\SMTP.php';


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
    //Verifica que el token esté activo y los permisos de usuario
    public function verifyToken($slug=false,$Permission='')
    {    
        if (isset($_SESSION['MAUTH']))        
        $token=$_SESSION['MAUTH'];
        else
            return false;

            

        $this->db->where('token',$token);
        $query=$this->db->get(DBTables['TK']);
        $tokenData=$query->row_array();
            if ($tokenData!=null)
            {
                if ($slug==FALSE)
                {
                    $this->db->where('User_Id',$tokenData['User_Id']);
                    $UserRol=(($this->db->get(DBTables['User']))->row_array())['Role_Id'];    

                    
                    if ($this->CheckPermission($UserRol,$Permission))
                        return true;
                    else
                        redirect('');                
                    
                    return false;
                }
                else 
                    redirect('');
                
            }
            else
            {
                if ($slug==FALSE)
                {
                    session_unset('MAUTH');
                    redirect('login');
                }
            }                     
    }

    private function CheckPermission($UserRol,$Permission)
    {
        $this->db->where('Permission_Id',$Permission);
        $this->db->where('Role_Id',$UserRol);
        
        if (($query=$this->db->get(DBTables['DPBR'])->row_array())==null)
            return true;
        else
            return false;




    }




    public function get_UserConnected()
    {
        $UserData=array();

        return $UserData;
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
    $this->SendEmailConfirmation($UserData,$ANIData['Token']);   
    
    return true;
    }

    private function SendEmailConfirmation($UserData,$Token)
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
        
        $URLConfirmation=base_url().'login/verificarcuenta/'.$Token;

        $message = "¡Bienvenido a  LoginPhp!Puedes autenticar tu cuenta ingresando a\n$URLConfirmation";
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('kikorial1234@gmail.com');
        $this->email->to($UserData[SYSAUTH]);
        $this->email->subject('Autenticación de la cuenta');
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
        // $mail->Password='lirycgdnkwahynku';
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





    private function randomToken($length = 10) { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 

}

?>