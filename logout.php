<?php
require("conf.php");
// Iniciar la sesión
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, se elimina la cookie de la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Eliminar todas las cookies
setcookie("nombre_cookie", "", time() - 3600, "/"); // Esto elimina la cookie

// Si tienes múltiples cookies, las eliminas de manera similar
setcookie("otra_cookie", "", time() - 3600, "/"); 

// Redirigir a la página de login o home
echo '<script type="text/javascript">

    window.location.href = "' . $url_base . 'index.php";

</script>';

exit();
?>