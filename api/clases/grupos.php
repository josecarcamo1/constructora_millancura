<?php
class Grupos{

    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Agregar
    public function agregar(){
        $nombre = $_POST["nombre"];
        $fecha_inicio = $_POST["fecha_inicio"];
        $estado = "ACTIVO";

        $sql = "INSERT INTO grupos (fecha_inicio,nombre,estado) VALUES (?,?,?)";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $fecha_inicio,
                    $nombre,
                    $estado
                ])){
                return true;
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    public function ultimo_id(){
        $sql = "SELECT id FROM grupos ORDER BY id DESC LIMIT 1";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchColumn();            
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Editar
    public function editar(){
        require "conn.php";

        $trabajo = $_POST["trabajo"];
        $descanso = $_POST["descanso"];
        $tipo_turno = $_POST["tipo_turno"];
        $dias_habiles = $_POST["dias_habiles"];
        $nombre = $_POST["nombre"];
        $comentario = $_POST["comentario"];
        $id = $_POST["id"];
          
        $sql = "UPDATE grupos SET 
            trabajo = ?,
            descanso = ?,
            tipo = ?,
            dias_habiles = ?,
            nombre = ?,
            comentario = ? 
        WHERE id = ?;";
        try{
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([
                    $trabajo,
                    $descanso,
                    $tipo_turno,
                    $dias_habiles,
                    $nombre,
                    $comentario,                    
                    $id
                ])){
                return "Turno Editado";
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
                    grupos.*,
                    clientes.nombre AS cliente
                FROM grupos
                LEFT JOIN clientes ON clientes.id = grupos.id_cliente";
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
        $id = $_POST["id"];
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM grupos WHERE id = ?");
            $stmt->execute([$id]);
            $results = $stmt->fetch();
            return $results;
        }catch(Exception $e) {
            die($e);
        }
    }

    //Borrar
    public function borrar(){
        $id = $_POST["id"];
        try{
            $stmt = $this->pdo->prepare("DELETE FROM grupos WHERE id = ?");
            $stmt->execute([$id]);
            return "Turno Borrado";
        }catch(Exception $e) {
            die($e);
        }
    }
}

?>