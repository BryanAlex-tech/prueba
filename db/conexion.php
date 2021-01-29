<?php 
require_once "config.php";

class BaseDatos
{

    private $conexion;  
    private $db; 

    public static function conectar() 
    {
        
        $conexion = mysqli_connect(host,user,pass,dbname);

        if($conexion->connect_errno)
            die("Lo sentimos, no se ha podido establecer la conexión con MySQL/MariaDB: ".mysqli_error($conexion));
        else
        {    
            $db = mysqli_select_db($conexion, dbname);
            if($db == 0)
                die("Lo sentimos, no se ha podido conectar con la base de datos: ".dbname);
        }
        return $conexion;
    }

    //Método para poder desconectar la conexion
    public function desconectar($conexion)
    {
        //Si la conexion existen entonces cerrarla
        if($conexion) 
            mysqli_close($conexion);
    }
}
?>