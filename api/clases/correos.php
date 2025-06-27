<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('html_errors', false);
header('Content-type: text/plain; charset=utf-8');
require '../libs/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Correos
{
    protected PDO $pdo;
    protected array $cred;
    protected $url;
    protected $mail;
    protected $mail_admin;
    protected $correo_admin;

    function __construct(PDO $pdo,array $cred){
        $this->pdo = $pdo;
        $this->cred = $cred;
        
        $this->url = $this->cred["url"];
        $this->correo_admin = $this->cred["admin"];

        $host = $this->cred["host"];
        $user = $this->cred["user"];
        $pass = $this->cred["pass"];
        

        $this->mail = new PHPMailer(true);
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;                         // Enable verbose debug output
        $this->mail->isSMTP();                                            // Send using SMTP
        $this->mail->Host       = $host;                                  // Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->mail->Username   = $user;                                  // SMTP username
        $this->mail->Password   = $pass;                                  // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mail->Port       = 587;                                    // TCP port to connect to

        //Origen
        $this->mail->setFrom($user, 'NEONLED');

        //Administrador (Copia de todos los correos)
        $this->mail->addBcc($this->correo_admin);
        
    }

    public function prueba(){

        $body = "<h1>Correo de Prueba</h1>
                <p>Host: " . $this->mail->Host . " </p>";

        $this->mail->addAddress($this->correo_admin);

        $this->mail->isHTML(true);
        $this->mail->Subject = 'Neonled | Prueba';
        $this->mail->Body = $body;

        try {
            if ($this->mail->send()) {
                return "Correo Enviado";
            } else {
                return "Error";
            }
        } catch (Exception $e) {
            die("Falla en envío de correo: " . $e);
        }
    }

    public function bienvenida($nombre, $correo, $pass){
        $this->mail->addAddress($correo);

        $body = '<div>
            <div style="padding:0 15px;max-width:700px">
                <h1 style="font-size:20px;font-weigth:bold;margin-bottom:30px">Acceso a plataforma de NEONLED.</h1>
                <p><strong>Nombre</strong>: ' . $nombre . '</p>
                <p><strong>Usuario</strong>: ' . $correo . '</p>
                <p><strong>Contraseña</strong>: ' . $pass . '</p>
                <p>Para acceder a la plataforma, dirigirse a: <a href="' . $this->url . '" target="_blank">link</a></p>                                
            </div>
        </div>';

        $this->mail->isHTML(true);
        $this->mail->Subject = 'Acceso NEONLED';
        $this->mail->Body = $body;

        try {
            //Agregar algun verificador y agregar a base de datos
            if ($this->mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die("Falla en envío de correo: " . $e);
        }
    }

    public function correo_traspaso(){

        //Correos de traspaso
        if(!$this->debug){
            $this->mail->AddCC('alejandro.deteran@gmail.com', 'Alejandro de Teran');
            //$this->mail->AddCC('icavassa@mpm.cl', 'Italo Cavassa');
        }

        $id_equipo = $_POST["id_equipo"];
        $id_traspaso = $_POST["id_traspaso"];
        $tipo_traspaso = $_POST["tipo_traspaso"];
        $movimiento = $_POST["movimiento"];
        
        //Info traspaso
        $sql = "SELECT 
            equipos_estados.*,
            clientes.nombre AS cliente
        FROM equipos_estados
        LEFT JOIN clientes ON clientes.id = equipos_estados.id_cliente
        WHERE equipos_estados.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_traspaso]);
            $traspaso = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            echo $e->getMessage(), "\n";
        }

        //Info equipo
        $sql = "SELECT 
            equipos.*,
            familias.nombre AS familia 
        FROM equipos
        LEFT JOIN familias ON equipos.id_familia = familias.id
        WHERE equipos.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_equipo]);
            $equipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            echo $e->getMessage(), "\n";
        }

        $body = '<div>
            <div style="padding:0 15px;max-width:700px">
                <p><strong>Equipo</strong>: ' . $equipo[0]["nombre"] . '</p>
                <p><strong>Patente</strong>: ' . $equipo[0]["patente"] . '</p>
                <p><strong>Movimiento</strong>: ' . $movimiento . '</p>
                <p>Para ver el traspaso, dirigirse a: <a href="' . $this->url . '" target="_blank">Link</a></p>                                
            </div>
        </div>';

        $this->mail->isHTML(true);
        $this->mail->Subject = 'Nuevo Traspaso ' . $equipo[0]["patente"];
        $this->mail->Body = $body;       
        
        $nombre_archivo = $tipo_traspaso . "_" . $equipo[0]["patente"] . "_" . $movimiento . ".pdf";
        $this->mail->addAttachment("../temp/" . $nombre_archivo,$nombre_archivo);

        try {
            if ($this->mail->send()) {
                unlink("../temp/" . $nombre_archivo);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die("Falla en envío de correo: " . $e);
        }        
    }

    function randomPassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
