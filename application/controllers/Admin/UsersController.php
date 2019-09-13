<?php


class UsersController extends CI_Controller
{
    public $Log;

    public function __construct()
    {

        parent::__construct();      
        $this->load->model('Admin/Authcheck');

        $this->Authcheck->verifyToken(false,Permissions::Acceder_Controller_Usuarios);



        
    }

    public function index()
    {
       
        $this->load->LayoutView();
    }

    public function PoliticaPrivacidad()
    {

        echo "esto es una prueba y los datos solo son para prueba, no los voy a vender realmente<br>";
        echo "solo se está usando el SDK de Facebook para hacer un login génerico para futuros proyectos en php<br>";
        echo "En caso de entrar si ya funciona algunos datos como su correo, nombre, sean almacenados<br>";
        echo "eventualmente serán borrados a medida se hagan pruebas o puede pedir borrarlos a gmcamiloe@gmail.com";
    }



    public function create()
    {

        echo "ahuevo";
    }




}


?>