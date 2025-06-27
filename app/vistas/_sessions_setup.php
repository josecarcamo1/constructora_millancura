<?php
session_start();
include("../../../../config/conn_neon.php");

//Setup
if(isset( $_SESSION['acceso']) && $_SESSION['acceso'] == "Vendedor" ){

    $id = $_SESSION['id'];
    $sql = "SELECT * FROM usuario_vendedor WHERE usuario_vendedor.id_usuario = ?";
    
    try{
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION["id"]]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e) {
        die($e);
    }

    $_SESSION['vendedores'] = $res;
}
?>