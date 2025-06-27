<?php
class Facturas{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Agregar
    public function nueva(){
        $id_proyecto = $_POST["id_proyecto"];        
        $valor = $_POST["valor"];
        $numero = $_POST["numero"];        
        $fecha = $_POST["fecha"];
        $comentario = $_POST["comentario"];

        $sql = "INSERT INTO facturas (
            id_proyecto,
            numero,
            valor,
            fecha,
            comentario
        ) 
        VALUES 
            (?,?,?,?,?);";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $id_proyecto,                    
                    $numero,
                    $valor,
                    $fecha,
                    $comentario
                ])){
                return true;
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    //Editar
    public function editar(){
        $trabajo = $_POST["trabajo"];
        $descanso = $_POST["descanso"];
        $tipo_turno = $_POST["tipo_turno"];
        $dias_habiles = $_POST["dias_habiles"];
        $nombre = $_POST["nombre"];
        $comentario = $_POST["comentario"];
        $id = $_POST["id"];
          
        $sql = "UPDATE pagos SET 
            trabajo = ?,
            descanso = ?,
            tipo = ?,
            dias_habiles = ?,
            nombre = ?,
            comentario = ? 
        WHERE id = ?;";
        try{
            $stmt = $this->pdo->prepare($sql);
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
                    facturas.*,
                    proyectos.nombre AS proyecto
                FROM facturas
                LEFT JOIN proyectos ON proyectos.id = facturas.id_proyecto";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Lista
    public function lista_id_proyecto(){
        $id_proyecto = $_POST["id_proyecto"];
        $res = [];    
        $sql = "SELECT
                    facturas.*,
                    proyectos.nombre AS proyecto
                FROM facturas
                LEFT JOIN proyectos ON proyectos.id = facturas.id_proyecto
                WHERE facturas.id_proyecto = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_proyecto]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Info
    public function info(){
        $id = $_POST["id"];
        $sql = "SELECT * FROM facturas WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
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
        $sql = "DELETE FROM facturas WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return "Factura Borrada";
        }catch(Exception $e) {
            die($e);
        }
    }
}

?>