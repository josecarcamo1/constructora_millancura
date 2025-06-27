<?php
session_start();

//Setup
// if( !isset( $_SESSION['contrato']) || $_SESSION['contrato'] == "Todos" ){
//     $res;
//     include "../../api/conn.php";

//     $sql = "SELECT
//             contratos.id as id_contr,
//             contratos.nombre,
//             contratos.codigo,
//             logins_contratos.*
//         FROM contratos
//         LEFT JOIN logins_contratos ON logins_contratos.id_contrato = contratos.id
//         WHERE contratos.estado = 'Activo'
//         AND logins_contratos.id_login = ?
//         ORDER BY nombre ASC
//         LIMIT 1";

//     try{
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([$_SESSION["id"]]);
//         $res = $stmt->fetchColumn();
//     }catch(Exception $e) {
//         die($e);
//     }    
//     $_SESSION['contrato'] = $res;
// }

// if(!isset( $_SESSION['fecha_tarja'])){       
//     $_SESSION['fecha_tarja'] = date("Y-m-d");
// }

// if(!isset( $_SESSION['rango_reporte_tarja'])){       
//     $_SESSION['rango_reporte_tarja'] = "Todo";
// }
?>