<?php
class Pagos{

    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Agregar
    public function nuevo(){
        $id_proyecto = $_POST["id_proyecto"];        
        $valor = $_POST["valor"];
        $comentario = $_POST["comentario"];
        $fecha = $_POST["fecha"];        

        $sql = "INSERT INTO pagos (
            id_proyecto,
            valor,
            comentario,
            fecha
        ) 
        VALUES 
            (?,?,?,?);";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $id_proyecto,
                    $valor,
                    $comentario,
                    $fecha
                ])){
                return true;
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
                    pagos.*,
                    proyectos.nombre AS proyecto
                FROM pagos
                LEFT JOIN proyectos ON proyectos.id = pagos.id_proyecto";
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
    public function info($id){              
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM pagos WHERE id = ?");
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
            $stmt = $this->pdo->prepare("DELETE FROM pagos WHERE id = ?");
            $stmt->execute([$id]);
            return "Pago Borrado";
        }catch(Exception $e) {
            die($e);
        }
    }
}

?>