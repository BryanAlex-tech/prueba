<?php
/*require_once establece que el código del archivo invocado es requerido, es decir, 
obligatorio para el funcionamiento del programa.*/
require_once "db/conexion.php";

class AlumnosDB
{
    protected $dbConn;
    protected $mysqliconn;

    /**
     * Constructor de clase
     */
    public function __construct()
    {
        try {
            //conexión a base de datos
            $this->mysqliconn = BaseDatos::conectar();
        } catch (mysqli_sql_exception $e) {
            //Si no se puede realizar la conexión
            http_response_code(500);
            exit;
        }
    }
    
    #region Consultas
    public function GetAlumno($id = 0)
    {
        $stmt = $this->mysqliconn->prepare("SELECT nombre, apellidos, carnet FROM alumnos WHERE idAlumno=? ; ");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($col1, $col2, $col3);
        $alumno = array();
        while ($stmt->fetch()) {
            $alumno[] = ['ID' => $id, 'nombre' => $col1, 'apellidos' => $col2, 'carnet' => $col3];
        }        
        $stmt->close();
        return $alumno;
    }

    public function GetAlumnos()
    {
        $result = $this->mysqliconn->query("SELECT * FROM alumnos");
        $alumnos= $result->fetch_all(MYSQLI_ASSOC);
        $result->close();
        return $alumnos;
    }

    function Consultas()
    {
        if ($_REQUEST['action'] == 'alumnos') {
            $db = new AlumnosDB();
            if (isset($_REQUEST['id'])) { //muestra 1 solo registro si es que existiera ID                 
                $response = $db->GetAlumno($_REQUEST['id']);
                echo json_encode($response, JSON_PRETTY_PRINT);
            } else { //muestra todos los registros                 
                $response = $db->GetAlumnos();
                echo json_encode($response, JSON_PRETTY_PRINT);
            }
        } 
        else {
            $this->response(400);
        }
    }
    #endregion
    
    #region Inserts
    public function Insert($nombre = '', $apellidos = '', $carnet = '')
    {
        $stmt = $this->mysqliconn->prepare("INSERT INTO alumnos(nombre,apellidos,carnet) VALUES(?,?,?);");
        $stmt->bind_param('sss', $nombre, $apellidos, $carnet);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    function SaveAlumno()
    {
        if ($_REQUEST['action'] == 'alumnos') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'));
            $objArr = (array)$obj;

            if (empty($objArr)) {
                $this->response(422, "error", "Nada que anadir. Comprobar json");
            } else if (isset($obj->nombre)) {
                $guardar = new AlumnosDB();
                $guardar->Insert($obj->nombre, $obj->apellidos, $obj->carnet);
                $this->response(200, "success", "Nuevo alumno agregado");
            } else {
                $this->response(422, "error", "La propiedad no está definida");
            }
        } else {
            $this->response(400);
        }
    }

    #endregion

    #region Delete

    public function Delete($id = 0)
    {
        $stmt = $this->mysqliconn->prepare("DELETE FROM alumnos WHERE idAlumno=? ;");
        $stmt->bind_param('i', $id);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    function DeleteAlumno()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'alumnos') {
                $db = new AlumnosDB();
                $db->Delete($_REQUEST['id']);
                $this->response(204, "success", "Alumno borrado");
                exit;
            }
        }
        $this->response(400);
    }

    #endregion

    #region Updates

    public function Update($id, $nombre, $apellidos, $carnet)
    {
        if ($this->CheckID($id)) {
            $stmt = $this->mysqliconn->prepare("UPDATE alumnos SET nombre=?, apellidos=?, carnet=? WHERE idAlumno=? ;");
            $stmt->bind_param('sssi', $nombre, $apellidos, $carnet, $id);
            $r = $stmt->execute();
            $stmt->close();
            return $r;
        }
        return false;
    }

    function UpdateAlumno()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'alumnos') {
                $obj = json_decode(file_get_contents('php://input'));
                $objArr = (array)$obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nada que actualizar. Comprobar json");
                } else if (isset($obj->nombre)) {
                    $db = new AlumnosDB();
                    $db->Update($_REQUEST['id'], $obj->nombre, $obj->apellidos, $obj->carnet);
                    $this->response(200, "success", "Datos actualizados");
                } else {
                    $this->response(422, "error", "La propiedad no esta definida");
                }
                exit;
            }
        }
        $this->response(400);
    }

    public function CheckID($id)
    {
        $stmt = $this->mysqliconn->prepare("SELECT * FROM alumnos WHERE idAlumno=?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                return true;
            }
        }
        return false;
    }

    #endregion

    //Método para generar los codigos de respuesta
    function response($code = 200, $status = "", $message = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }
}
