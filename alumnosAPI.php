<?php

require_once "alumnosDB.php";

class AlumnosAPI {
    
    protected $alumnoDB; 
    
    public function __construct() { 
        $this->alumnoDB = new AlumnosDB();          
    }

    public function API(){

        header('Content-Type: application/json');  

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
        case 'GET'://Si es GET hace una consulta
            $this->alumnoDB->Consultas();
            break;     
        case 'POST'://Si es POST inserta
            $this->alumnoDB->SaveAlumno();
            break;                
        case 'PUT'://Si es PUT actualiza
            $this->alumnoDB->UpdateAlumno();
            break;      
        case 'DELETE'://Obviamente DELETE elimina
            $this->alumnoDB->DeleteAlumno();
            break;
        default://metodo NO soportado
            $this->alumnoDB->response(405);
            break;
        }
    }
    
}
?>