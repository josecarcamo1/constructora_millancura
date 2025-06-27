<?php
session_start();
error_reporting(0);

// Desactivar caché de la página
header("Cache-Control: no-cache, no-store, must-revalidate"); // Deshabilita caché
header("Pragma: no-cache"); // Para navegadores antiguos
header("Expires: 0"); // Asegura que no haya expiración





$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor

$dbname = 'dbzgnsjprpeglc'; // Nombre de tu base de datos

$user = 'um9oijpx9digr'; // Usuario de la base de datos

$pass = '@~2fhw&f2b&1'; // Contraseña del usuario de la base de datos



$url_base = "https://www.agenciaimola.cl/constructora_millancura/"; // Cambia esto según la ubicación de tu proyecto



try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}



if (!isset($pdo)) {

    die("Error: No se pudo establecer la conexión con la base de datos.");

}

require("leer_json.php");

// Ejemplo de uso
$resultado = procesarArchivosCamara($pdo);

// if (is_array($resultado)) {
//     echo "<h3>Datos del archivo JSON:</h3>";
//     echo "<pre>" . json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
//     // También podés acceder a campos específicos:
//     // $plate = $resultado['infoplate']['Plate'] ?? 'No disponible';
//     // echo "<p><strong>Placa detectada:</strong> $plate</p>";
// } else {
//     echo "<p style='color: red;'><strong>Error:</strong> $resultado</p>";
// }
?>