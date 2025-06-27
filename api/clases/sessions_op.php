<?php

session_start();

if($_POST["tipo"] == "leer"){
    echo json_encode($_SESSION);
}

// if($_POST["tipo"] == "cambiar"){
//     if($_POST["var"] == "contrato"){
//         $_SESSION["contrato"] = $_POST["nuevo_contrato"];
//         echo $_SESSION["contrato"];
//     }

//     if($_POST["var"] == "fecha_tarja"){
//         $_SESSION["fecha_tarja"] = $_POST["nueva_fecha_tarja"];
//         echo $_SESSION["fecha_tarja"];
//     }

//     if($_POST["var"] == "rango_reporte_tarja"){
//         $_SESSION["rango_reporte_tarja"] = $_POST["nuevo_rango_reporte_tarja"];
//         echo $_SESSION["rango_reporte_tarja"];
//     }
// }

if($_POST["tipo"] == "cerrar"){
    session_destroy();
}

?>