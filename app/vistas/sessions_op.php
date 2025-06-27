<?php

session_start();

if($_POST["tipo"] == "leer"){
    echo json_encode($_SESSION);
}

if($_POST["tipo"] == "cerrar"){
    session_destroy();
}

?>