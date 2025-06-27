<?php
class Vendedores{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // //Agregar
    public function agregar(){

        $nombre = $_POST["nombre"];
        $color = $_POST["color"];

        $sql = "INSERT INTO vendedores (nombre,estado,color) 
        VALUES (?,?,?);";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $nombre,
                    "Activo",
                    $color
                ])){
                return "Vendedor Agregado";
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    // //Editar
    public function editar(){

        $nombre = $_POST["nombre"];
        $estado = $_POST["estado"];
        $color = $_POST["color"];
        $id = $_POST["id_vendedor"];
          
        $sql = "UPDATE vendedores SET 
            nombre = ?,
            estado = ?,
            color = ?
        WHERE id = ?;";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([$nombre,$estado,$color,$id])){
                return "Vendedor Editado";
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    //Listas
    public function lista(){
        
        $res = [];    
        $sql = "SELECT * FROM vendedores";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }
        return ["draw"=> 1,"recordsTotal"=> count($res),"recordsFiltered"=> count($res),"data"=>$res];
    }

    public function lista_selector(){        
        $res = [];    
        $sql = "SELECT * FROM vendedores WHERE estado = 'Activo'";
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
        $id_vendedor = $_POST["id_vendedor"];

        $sql = "SELECT * FROM vendedores WHERE vendedores.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_vendedor]);
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
            $stmt = $this->pdo->prepare("DELETE FROM vendedores WHERE id = ?");
            $stmt->execute([$id]);
            return "Vendedor Borrado";
        }catch(Exception $e) {
            die($e);
        }
    }
}

?>