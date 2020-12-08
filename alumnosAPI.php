<?php
//requerimos el archivo worldDB que contiene la logica y consultas a la base de datos
require_once "alumnosDB.php";

class AlumnosAPI {
    
    protected $alumnoDB; //Atributo de clase
    
    //Creando el método constructor de la clase
    public function __construct() { 
        $this->alumnoDB = new AlumnosDB();          
    }

    /*Método publico que ejecuta la llamada a una operacion
     con la base de datos dependiendo del metodo http utilizado
     para realizar la peticion a la API*/
    public function API(){

        /*Muchas veces es bueno definir el tipo de contenido que estamos mostrando, 
        así los navegadores podrán tratarlos como es debido y adaptarse a los estándares de estos.
        En el caso de JSON muchas veces lo usamos para comunicar el Front-end con el Back-end 
        pero un error que la mayoría hace es enviarlo como html plano en vez de decir que es de tipo Json, 
        por eso vamos a definirlo ahora. */
        header('Content-Type: application/json');  

        //$_SERVER['REQUEST_METHOD']: Devuelve el método de solicitud utilizado para acceder a la página (como POST)
        $method = $_SERVER['REQUEST_METHOD'];

        //Evaluamos el méetodo http utilizado para la llamada a la API
        switch ($method) {
        case 'GET'://Si es GET hace una consulta
            $this->alumnoDB->Consultas();
            break;     
        case 'POST'://Si es POST inserta
            $this->alumnoDB->SaveAlumno();
            break;                
        case 'PUT'://Si es PUT actualiza
            $this->alumnoDB->UpdateCity();
            break;      
        case 'DELETE'://Obviamente DELETE elimina
            $this->alumnoDB->DeleteCity();
            break;
        default://metodo NO soportado
            $this->alumnoDB->response(405);
            break;
        }
    }
    
}//end class
?>