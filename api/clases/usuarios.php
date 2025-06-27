<?php
ini_set('html_errors', false);

class Usuarios{
    protected PDO $pdo;
    protected array $cred;
    
    function __construct(PDO $pdo,array $cred) {
        $this->pdo = $pdo;
        $this->cred = $cred;
    }
    
    public function lista()
    {
        
        $sql = "SELECT 
            usuarios.id,
            usuarios.nombre,
            usuarios.usuario,
            usuarios.estado,
            usuarios.acceso
         FROM usuarios ORDER BY nombre ASC";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e);
        }
        return $res;
    }

    public function info($id)
    {       
        $sql = "SELECT 
            usuarios.id,
            usuarios.nombre,
            usuarios.usuario,
            usuarios.estado,
            usuarios.acceso
         FROM usuarios
         WHERE usuarios.id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e);
        }
        return $res;
    }

    public function agregar($nombre,$correo,$acceso)
    {
        include_once "correos.php";
        $correos = new Correos($this->pdo,$this->cred);      

        $contr = randomKey(8);
        $jwt = randomKey(10);        

        $sql = "INSERT INTO usuarios (nombre,usuario,contr,id_jwt,estado,acceso)
        VALUES (?,?,?,?,?,?)";
        try {
            $stmt = $this->pdo->prepare($sql);

            if($correos->bienvenida($nombre,$correo, $contr)){
                if($stmt->execute([$nombre, $correo, password_hash($contr, PASSWORD_DEFAULT),$jwt,1,$acceso])){
                    return "Usuario Creado y correo enviado.";
                }else {
                    if ($stmt->errorInfo()[0] == 23000) {
                        return "Correo ya existe";
                    } else {
                        return $stmt->errorInfo()[0];
                    }
                }
            }else{
                return "Hubo en el envío del correo, vuelva a intentar más tarde";
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    public function ultimo_id()
    {
        
        $sql = "SELECT usuarios.id
            FROM usuarios ORDER BY usuarios.id DESC LIMIT 1";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e);
        }
        return $res;
    }

    public function actualizar($id,$nombre,$estado,$acceso)
    {
        
        $sql = "UPDATE usuarios SET
        nombre = ?,
        estado = ?,
        acceso = ?
        WHERE usuarios.id = ?
        ";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nombre,$estado, $acceso, $id]);
        } catch (Exception $e) {
            die($e);
        }
        return "Usuario Actualizado";
    }

    public function borrar($id)
    {
        
        $sql = "DELETE FROM usuarios WHERE usuarios.id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                return "Usuario Borrado";
            } else {
                return $stmt->infoError();
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    public function lista_vendedores_usuario($id_usuario)
    {
        
        $sql = "SELECT 
                    vendedores.nombre AS vendedor, 
                    usuario_vendedor.id AS id_uv,
                    usuario_vendedor.id_usuario,
                    usuario_vendedor.id_vendedor
                FROM usuario_vendedor 
                LEFT JOIN vendedores ON vendedores.id = usuario_vendedor.id_vendedor
                WHERE usuario_vendedor.id_usuario = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_usuario]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e);
        }
        return $res;
    }

    public function verificar_contr($correo,$contr,$id)
    {   
        $sql = "SELECT contr FROM usuarios WHERE usuario = ? AND id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$correo,$id]);
            $res = $stmt->fetchColumn();            
            if(password_verify($contr,$res)){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            die($e);
        }
    }

    public function agregar_vendedor($id_usuario,$id_vendedor){
        $sql = "INSERT INTO usuario_vendedor (id_usuario,id_vendedor) VALUES(?,?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_usuario,$id_vendedor]);
        } catch (Exception $e) {
            die($e);
        }
        return "Vendedor Agregado";
    }

    public function check_vendedor($id_usuario,$id_vendedor){
        $sql = "SELECT id FROM usuario_vendedor WHERE id_usuario = ? AND id_vendedor = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_usuario,$id_vendedor]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($res) > 0){
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
            die($e);
        }
        return "Vendedor Agregado";
    }

    public function borrar_vendedor($id){
        $sql = "DELETE FROM usuario_vendedor WHERE usuario_vendedor.id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$id])) {
                return "Vendedor Borrado";
            } else {
                return $stmt->infoError();
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    // public function reestablecer_contr($id,$correo)
    // {
    //     // include_once "correos.php";
    //     $correos = new Correos($this->pdo);
        

    //     $contr = randomPassword();
    //     $hash_contr = password_hash($contr, PASSWORD_DEFAULT);

    //     $sql = "UPDATE usuarios SET
    //     contr = ?
    //     WHERE usuarios.usuario = ? AND usuarios.id = ?
    //     ";
    //     try {
    //         $stmt = $this->pdo->prepare($sql);            
    //         if ($stmt->execute([$hash_contr,$correo,$id])) {
    //             if ($correos->correo_reset_contr($correo, $contr)) {
    //                 return "Contraseña Reestrablecida.";
    //             } else {
    //                 return "Hubo un error, vuelva a intentar más tarde";
    //             }
    //         } else {
    //             if ($stmt->errorInfo()[0] == 23000) {
    //                 return "Correo ya existe";
    //             } else {
    //                 return $stmt->errorInfo()[0];
    //             }
    //         }
    //     } catch (Exception $e) {
    //         die($e);
    //     }
    //     return "Contraseña Actualizada";
    // }
        
    private function actualizar_ctr($correo,$contr,$id){
        
        $sql = "UPDATE usuarios SET
            contr = ?
            WHERE usuarios.correo = ? AND usuarios.id = ?
        ";
        try {
            $stmt = $this->pdo->prepare($sql);            
            if ($stmt->execute([$contr,$correo,$id])){
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
            die($e);
        }
    }

    public function cambiar_contr($id,$correo,$contr_nueva)
    {
        
        $sql = "UPDATE usuarios SET        
        contr = ?
        WHERE usuarios.id = ? AND usuarios.usuario = ?
        ";

        $contr = password_hash($contr_nueva, PASSWORD_DEFAULT);
        try {
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([$contr,$id,$correo])){
                return "Contraseña Acualizada";
            }else{
                return "Hubo un error " . $stmt->errorInfo();
            }
        } catch (Exception $e) {
            die($e);
        }
    }
}

function randomKey($tam)
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $contr = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $tam; $i++) {
        $n = rand(0, $alphaLength);
        $contr[] = $alphabet[$n];
    }
    return implode($contr);
}
