<?php
class Sesiones{

    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        session_start();
        $this->pdo = $pdo;
    }

    public function crear(){        
        if($_SESSION['acceso'] == "Vendedor" ){
            $id = $_SESSION['id'];
            $sql = "SELECT 
                        * 
                    FROM usuario_vendedor 
                    LEFT JOIN vendedores ON vendedores.id = usuario_vendedor.id_vendedor
                    WHERE usuario_vendedor.id_usuario = ?
                    AND vendedores.estado = 'Activo'";
        
            try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$_SESSION["id"]]);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e) {
                die($e);
            }
        
            $_SESSION['vendedores'] = $res;
            $_SESSION["id_vendedor"] = $res[0]["id"];
        }elseif ($_SESSION["acceso"] == "Administrador"){
            $sql = "SELECT * FROM vendedores WHERE vendedores.estado = 'Activo'";
        
            try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch(Exception $e) {
                die($e);
            }
        
            $_SESSION['vendedores'] = $res;
            $_SESSION["id_vendedor"] = $res[0]["id"];
        }
        return "Ok";
    }

    public function usuario(){
        $usuario = [$_SESSION["id"],$_SESSION["usuario"],$_SESSION["nombre"],$_SESSION["acceso"]];
        echo json_encode($usuario);
    }
    
    public function leer_sesion(){
        echo json_encode($_SESSION);
    }

    public function check_sess(){
        if(isset($_SESSION["loggedin_neonled"]) && $_SESSION["loggedin_neonled"] === true){    
            exit("200");
        }else{
            exit("204");
        }
    }
    
    public function cambiar(){
        $tipo_sesion = $_POST["tipo_sesion"];
        $nuevo = $_POST["nuevo"];
        
        if($tipo_sesion == "Vendedor"){
            $_SESSION["id_vendedor"] = $nuevo;
            return;
        }
    }
}

?>