<?php
session_start();
include("../../../../config/conn_neon.php"); 

if($_SESSION['acceso'] == "Vendedor" ){
    $id = $_SESSION['id'];
    $sql = "SELECT 
                * 
            FROM usuario_vendedor 
            LEFT JOIN vendedores ON vendedores.id = usuario_vendedor.id_vendedor
            WHERE usuario_vendedor.id_usuario = ?
            AND vendedores.estado = 'Activo'";

    try{
        $stmt = $pdo->prepare($sql);
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
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e) {
        die($e);
    }

    $_SESSION['vendedores'] = $res;
    $_SESSION["id_vendedor"] = $res[0]["id"];
}

?>