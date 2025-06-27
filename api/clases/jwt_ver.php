<?php
class JwtVer{
    protected String $key;
    
    function __construct(String $key) {
        $this->key = $key;
    }
    
    public function verificar(){
        $token = null;
        $response = "";
        if(isset($_COOKIE["token"])) {
            $token = $_COOKIE["token"];
        }

        if(!is_null($token)) {
            require_once('jwt.php');
            try {
                $payload = JWT::decode($token, $this->key, array('HS256'));
                if(isset($payload->id_usuario)){
                    $response = "Ok";
                }
            }
            catch(Exception $e) {
                $response = "Token Inválido";
            }    
        }else{
            $response = "";
        }
        return $response;
    } 
}

?>