<?php

class Regiones_comunas{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function lista_comunas(){        
               
        $sql = "SELECT * FROM comun_comunas ORDER BY comun_comunas.nombre ASC";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;        
        }catch(Exception $e) {
            die($e);
        }
    }

    public function prov_reg($id_provincia){
               
        $res = [];
        $sql = "SELECT * FROM comun_provincias WHERE comun_provincias.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_provincia]);
            $results = $stmt->fetch();
            $res[0] = $results;            
        }catch(Exception $e) {
            die($e);
        }
        $id_region = $res[0]["id_region"];
        $sql_reg = "SELECT * FROM comun_regiones WHERE comun_regiones.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql_reg);
            $stmt->execute([$id_region]);
            $results = $stmt->fetch();
            $res[1] = $results;
        }catch(Exception $e) {
            die($e);
        }

        return $res;
    }
}

?>