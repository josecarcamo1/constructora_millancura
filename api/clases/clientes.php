<?php
class Clientes{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // //Agregar
    public function agregar(){

        $nombre = $_POST["nombre"];
        $rut = $_POST["rut"];
        $direccion = $_POST["direccion"];
        $telefono = $_POST["telefono"];
        $correo = $_POST["correo"];
        $comuna = $_POST["comuna"];
        $provincia = $_POST["provincia"];
        $region = $_POST["region"];

        $sql = "INSERT INTO clientes (
            nombre,
            rut,
            direccion,
            comuna,
            provincia,
            region,
            telefono,
            mail
        ) 
        VALUES 
            (?,?,?,?,?,?,?,?);";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $nombre,
                    $rut,
                    $direccion,
                    $comuna,
                    $provincia,
                    $region,
                    $telefono,
                    $correo
                ])){
                return "Cliente Agregado";
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    public function agregar_basico($cliente){
        if($this->basico($cliente)){
            return $this->ultimo_id();
        }
    }

    public function basico($cliente){
        $sql = "INSERT INTO clientes (nombre) VALUES (?)";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([$cliente])){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    private function ultimo_id(){
        $res = [];    
        $sql = "SELECT id FROM clientes ORDER BY id DESC LIMIT 1";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchColumn();
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    // //Editar
    public function editar(){

        $nombre = $_POST["nombre"];
        $rut = $_POST["rut"];
        $direccion = $_POST["direccion"];
        $telefono = $_POST["telefono"];
        $correo = $_POST["correo"];
        $comuna = $_POST["comuna"];
        $provincia = $_POST["provincia"];
        $region = $_POST["region"];
        $id = $_POST["id"];
          
        $sql = "UPDATE clientes SET 
            nombre = ?,
            rut = ?,
            direccion = ?,
            telefono = ?,
            mail = ?,
            comuna = ?,
            provincia = ?,
            region = ?
        WHERE id = ?;";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                $nombre,
                $rut,
                $direccion,
                $telefono,
                $correo,
                $comuna,
                $provincia,
                $region,
                $id
            ])){
                return "Cliente Editado";
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    //Lista
    public function lista(){
        
        $res = [];    
        $sql = "SELECT 
                clientes.id,
                clientes.rut,
                clientes.nombre, 
                clientes.telefono,
                clientes.mail,
                count(proyectos.id) AS total
            FROM clientes
            LEFT JOIN proyectos ON proyectos.id_cliente = clientes.id
            GROUP BY clientes.id";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Info
    public function info(){
        $id_cliente = $_POST["id_cliente"];

        $sql = "SELECT 
            clientes.*,
            comun_comunas.nombre_may AS comuna,
            comun_provincias.nombre_may AS provincia,
            comun_regiones.nombre_may AS region
            FROM clientes
            LEFT JOIN comun_comunas ON comun_comunas.id = clientes.comuna
            LEFT JOIN comun_provincias ON comun_provincias.id = clientes.provincia
            LEFT JOIN comun_regiones ON comun_regiones.id = clientes.region
            WHERE clientes.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_cliente]);
            $results = $stmt->fetch();
            return $results;
        }catch(Exception $e) {
            die($e);
        }
    }

    // //Borrar
    public function borrar(){
        $id = $_POST["id"];
        try{
            $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);
            return "Cliente Borrado";
        }catch(Exception $e) {
            die($e);
        }
    }
}

?>