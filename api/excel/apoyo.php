<?php

class Apoyo{

    public function ventas_rango($desde,$hasta){
        include("../../../config/conn_neon.php");

        $sql = "SELECT 
            proyectos.*,
            clientes.nombre AS cliente
        FROM proyectos
        LEFT JOIN clientes ON clientes.id = proyectos.id_cliente
        WHERE DATE(proyectos.fecha_inicio) >= :desde
        AND DATE(proyectos.fecha_inicio) <= :hasta";

        try {
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':desde',$desde,PDO::PARAM_STR);            
            $stmt->bindParam(':hasta',$hasta,PDO::PARAM_STR);

            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        } catch (Exception $e) {
            die("Hubo un error, inténtelo nuevamente " . $e->getMessage());
        }
    }

    public function formato_fecha($fecha){
        return date("d/m/Y", strtotime($fecha));
    }

    public function formato_valor($valor){
        return "$".number_format($valor, 2, '.', '');
    }
}