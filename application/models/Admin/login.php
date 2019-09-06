<?php

defined('BASEPATH') or exit('No se encontró');
defined('SYSAUTH') or exit('NAN');
defined('SYSPASS') or exit('NAN');


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
        $query=$this->db->get('user');
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
        

        $this->db->insert('tokenUser',$TokenData);
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
        $query=$this->db->get('tokenuser');
        $tokenData=$query->row_array();
            if ($tokenData!=null)
            {
                if ($slug==FALSE)
                {
                    $this->db->where('User_Id',$tokenData['User_Id']);
                    $UserRol=(($this->db->get('user'))->row_array())['Role_Id'];    

                    
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
        
        if (($query=$this->db->get('dpbr')->row_array())==null)
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
       $this->db->insert('user',$UserData);
       return true;
    }

    private function randomToken($length = 10) { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 
    

}

?>