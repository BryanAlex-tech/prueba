<?php
    //Solucion para poder utilizar el archivo .htaccess
    //http://stackoverflow.com/questions/18382740/cors-not-working-php
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        
            {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }    
    
    /*require_once establece que el código del archivo invocado es requerido, es decir, 
    obligatorio para el funcionamiento del programa.*/    
    require_once "alumnosAPI.php";  

    //Creando objeto a partir de la clase WorldAPI
    $alumnosAPI = new AlumnosAPI();

    //Utilizando la instancia creada para poder acceder al método API()
    $alumnosAPI->API();
?>
