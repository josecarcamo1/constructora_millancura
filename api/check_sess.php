<?php
session_start();
if(isset($_SESSION["loggedin_neonled"]) && $_SESSION["loggedin_neonled"] === true){    
    exit("200");
}else{
    exit("204");
}
?>