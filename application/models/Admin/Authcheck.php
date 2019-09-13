<?php
defined('BASEPATH');
defined('DBTables');
defined('SYSAUTH');

class Authcheck extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
        if(!isset($_SESSION)) 
        session_start();
    }

      //Verifica que el token esté activo y los permisos de usuario
      public function verifyToken($slug=false,$Permission='')
      {    

         
         
          if (isset($_SESSION['MAUTH']))        
          $token=$_SESSION['MAUTH'];
          else
              return false;
  
              
          $this->db->where('token',$token);
          $query=$this->db->get(DBTables['tokenuser']);
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
             
           redirect(base_url().'login/permisos/fd');

            
      }
  
      public function get_UserConnected()
      {
        $UserData=array();
        if (!isset($_SESSION['MAUTH'])) 
            return null;
        
        $token=$_SESSION['MAUTH'];
        $this->db->where('token',$token);
        $User_Id=(($this->db->get(DBTables['tokenuser']))->row_array())['User_Id'];

        if ($User_Id==null)
            return null;
        
        $this->db->where('User_Id',$User_Id);
        $UserData=($this->db->get(DBTables['User']))->row_array();
        
          return $UserData;
      }

}




?>