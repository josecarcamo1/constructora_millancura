<?php
ini_set('html_errors', false);
include "config.php";

if(session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
session_start();

if(isset($_SESSION["loggedin_neonled"]) && $_SESSION["loggedin_neonled"] === true){    
    exit("200");
}

if ( !isset($_POST['usuario'], $_POST['contr']) || $_POST["usuario"] == "" || $_POST["contr"] == "") {	
    exit('Debe ingresar un usuario y contraseña');
}

$sql = "SELECT id,id_jwt,estado,nombre,usuario,acceso,contr FROM usuarios WHERE usuario = ?";
try{
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$_POST["usuario"]])){        
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($res) > 0){            
            $id = $res[0]["id"];
            $id_jwt = $res[0]["id_jwt"];
            $estado = $res[0]["estado"];
            $nombre = $res[0]["nombre"];
            $correo = $res[0]["usuario"];
            $acceso = $res[0]["acceso"];
            $contr = $res[0]["contr"];
           

            if($estado == 1){
                if (password_verify($_POST['contr'], $contr)) {
                    require_once('clases/jwt.php');
                    //Token
                    $hoy = date('d-m-Y H:i:s',time());                    
                    $nbf = time();
                    $payloadArray = array();
                    $payloadArray["id_usuario"] = $id_jwt;
                    $payloadArray['nbf'] = $nbf;
                    $payloadArray['exp'] = time() + (86400 * 30);
                    $token = JWT::encode($payloadArray, $key);
                    setcookie("token",$token,[
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'None'
                    ]);

                    // session_regenerate_id();                    
                    $_SESSION['loggedin_neonled'] = TRUE;
                    $_SESSION['usuario'] = $correo;
                    $_SESSION['nombre'] = $nombre;
                    $_SESSION['acceso'] = $acceso;
                    $_SESSION['id'] = $id;
                    if(isset($_SESSION['id'])){
                        echo json_encode(["code" => "200", "message" => "OK"]);
                    }else{
                        echo json_encode(["error" => "Error al crear sesiones."]);                        
                    }                    
                } else {
                    echo json_encode(["error" => "Error de autenticación"]);
                }
            }else{
                echo json_encode(["error" => "Error de autenticación"]);
            }
        }else{
            echo json_encode(["error" => "Error de autenticación"]);
        }
    }else{
        echo $stmt->errorInfo();
    };
}catch(Exception $e) {
    error_log("Error: " . $e->getMessage());
    return ["error" => $e->getMessage()];
}

// try {
//     $stmt = $pdo->prepare($sql);
    
//     // Check if query execution was successful
//     if ($stmt->execute([$_POST["usuario"]])) {        
//         $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
//         if (count($res) > 0) {            
//             $id = $res[0]["id"];
//             $id_jwt = $res[0]["id_jwt"];
//             $estado = $res[0]["estado"];
//             $nombre = $res[0]["nombre"];
//             $correo = $res[0]["usuario"];
//             $acceso = $res[0]["acceso"];
//             $contr = $res[0]["contr"];

//             // Check if the user account is active
//             if ($estado == 1) {
//                 // Verify password
//                 if (password_verify($_POST['contr'], $contr)) {
//                     require_once('clases/jwt.php');
                    
//                     // Generate JWT token
//                     $payloadArray = [
//                         "id_usuario" => $id_jwt,
//                         'nbf' => time(),
//                         'exp' => time() + (86400 * 30)
//                     ];
//                     $token = JWT::encode($payloadArray, $key);

//                     // Set cookie for the token
//                     setcookie("token", $token, [
//                         'expires' => time() + (86400 * 30),
//                         'path' => '/',
//                         'secure' => true,
//                         'httponly' => true,
//                         'samesite' => 'None'
//                     ]);

//                     // Regenerate session ID to prevent session fixation
//                     if (!session_regenerate_id(true)) {
//                         throw new Exception("Failed to regenerate session ID.");
//                     }

//                     // Set session variables
//                     $_SESSION['loggedin_neonled'] = true;
//                     $_SESSION['usuario'] = $correo;
//                     $_SESSION['nombre'] = $nombre;
//                     $_SESSION['acceso'] = $acceso;
//                     $_SESSION['id'] = $id;

//                     if(count($_SESSION) == 0){
//                         return ["error" => "Error al crear sesiones."];
//                     }else{
//                         return ["status" => "200 OK"];
//                     }                    
//                 } else {
//                     return ["error"=>'Usuario o Contraseña Inválido'];
//                 }
//             } else {
//                 return ["error"=>"Usuario desactivado"];
//             }
//         } else {
//             return ["error"=>'Usuario no existe'];
//         }
//     } else {
//         // Log and return database execution error
//         error_log("Database error: " . implode(", ", $stmt->errorInfo()));
//         return ["error"=>"Error en el servidor. Intente más tarde."];
//     }
// } catch (Exception $e) {
//     // Log exception details and return an error message
//     error_log("Error: " . $e->getMessage());
//     return ["error"=>"Error en el servidor. Intente más tarde."];
// }
?>
