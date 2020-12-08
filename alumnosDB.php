<?php
/*require_once establece que el código del archivo invocado es requerido, es decir, 
obligatorio para el funcionamiento del programa.*/
require_once "db/conexion.php";

class AlumnosdDB
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
    public function GetCity($id = 0)
    {
        $stmt = $this->mysqliconn->prepare("SELECT Name, CountryCode, District, Population FROM city WHERE ID=? ; ");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($col1, $col2, $col3, $col4);
        $city = array();
        while ($stmt->fetch()) {
            $city[] = ['ID' => $id, 'Name' => $col1, 'CountryCode' => $col2, 'District' => $col3, 'Population' => $col4];
        }        
        $stmt->close();
        return $city;
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
            $db = new AlumnosdDB();
            if (isset($_REQUEST['id'])) { //muestra 1 solo registro si es que existiera ID                 
                $response = $db->GetCity($_REQUEST['id']);
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
    public function Insert($name = '', $cc = '', $district = '', $population = '')
    {
        $stmt = $this->mysqliconn->prepare("INSERT INTO city(Name,CountryCode,District,Population) VALUES(?,?,?,?);");
        $stmt->bind_param('sssi', $name, $cc, $district, $population);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    function SaveCity()
    {
        if ($_REQUEST['action'] == 'ciudades') {
            //Decodifica un string de JSON
            $obj = json_decode(file_get_contents('php://input'));
            $objArr = (array)$obj;

            if (empty($objArr)) {
                $this->response(422, "error", "Nada que anadir. Comprobar json");
            } else if (isset($obj->name)) {
                $city = new WorldDB();
                $city->Insert($obj->name, $obj->countryCode, $obj->district, $obj->population);
                $this->response(200, "success", "Nueva ciudad agregada");
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
        $stmt = $this->mysqliconn->prepare("DELETE FROM city WHERE ID=? ;");
        $stmt->bind_param('i', $id);
        $r = $stmt->execute();
        $stmt->close();
        return $r;
    }

    function DeleteCity()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'ciudades') {
                $db = new WorldDB();
                $db->Delete($_REQUEST['id']);
                $this->response(204);
                exit;
            }
        }
        $this->response(400);
    }

    #endregion

    #region Updates

    public function Update($id, $newName, $newCC, $newDistrict, $newPopulation)
    {
        if ($this->CheckID($id)) {
            $stmt = $this->mysqliconn->prepare("UPDATE city SET Name=?, CountryCode=?, District=?, Population=? WHERE ID=? ;");
            $stmt->bind_param('sssii', $newName, $newCC, $newDistrict, $newPopulation, $id);
            $r = $stmt->execute();
            $stmt->close();
            return $r;
        }
        return false;
    }

    function UpdateCity()
    {
        if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
            if ($_REQUEST['action'] == 'ciudades') {
                $obj = json_decode(file_get_contents('php://input'));
                $objArr = (array)$obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nada que actualizar. Comprobar json");
                } else if (isset($obj->name)) {
                    $db = new WorldDB();
                    $db->Update($_REQUEST['id'], $obj->name, $obj->countryCode, $obj->district, $obj->population);
                    $this->response(200, "success", "Ciudad actualizada");
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
        $stmt = $this->mysqliconn->prepare("SELECT * FROM city WHERE ID=?");
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
