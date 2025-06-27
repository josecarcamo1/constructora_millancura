<?php
class Carga{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Clientes
    public function clientes(){
        $total = 0;
        $lista = json_decode($_POST["lista"]);        

        for($i = 0; $i < count($lista);$i++){
            if($this->cliente_agregar_unico($lista[$i][0])){
                $total++;
            }
        }
        return $total . " Clientes agregados";
    }

    private function cliente_agregar_unico($nombre){        
        $sql = "INSERT INTO clientes (nombre) VALUES (?)";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nombre]);            
        }catch(Exception $e) {
            die($e);
        }
    }


    //Proyectos

    public function proyectos(){
        /*
            1.Crear proyecto
        */

        $total = 0;
        $lista = json_decode($_POST["lista"]);
        // echo json_encode($lista[0]);
        for($i = 1; $i < count($lista);$i++){
        // for($i = 1; $i < 5;$i++){
            $numero = $lista[$i][0];
            $fecha_inicio = $lista[$i][1];
            $cliente = $lista[$i][2];
            $trabajo = $lista[$i][3];
            $vendedor = $lista[$i][4];
            //$neonista = $lista[$i][5];
            $valor = intval($lista[$i][6]);
            $pagado = intval($lista[$i][7]);
            $por_pagar = intval($lista[$i][8]);
            $iva = intval($lista[$i][9]);
            $valor_final = intval($lista[$i][10]);
            // $forma_pago = $lista[$i][11];
            $id_vendedor = intval($lista[$i][12]);
            $id_cliente = 0;
            $estado = "TERMINADO";
            $estado_iva = 0;

            if($por_pagar > 0){
                $estado = "ACTIVO";
            }

            if($iva > 0){
                $estado_iva = 1;
            }

            // echo json_encode($trabajo);
            // echo json_encode($valor);

            // echo json_encode($cliente);
            // echo json_encode($this->check_cliente_nombre($cliente));

            if($this->check_cliente_nombre($cliente)){
                echo "Ya existe" . PHP_EOL;
                $id_cliente = $this->check_cliente_nombre($cliente)[0]["id"];
            }else{
                echo "Agregar" . PHP_EOL;
                $this->cliente_agregar_unico($cliente);
                $id_cliente = $this->ultimo_id_cliente()[0]["id"];
            }

            // echo json_encode($id_cliente[0]["id"]);          

            $this->proyectos_agregar_unico($numero,$id_vendedor,$id_cliente,$trabajo,$valor,$fecha_inicio,$estado,$estado_iva);
            $total++;
        }
        // return $total . " Proyectos agregados";
    }

    private function proyectos_agregar_unico($numero,$id_vendedor,$id_cliente,$trabajo,$valor,$fecha_inicio,$estado,$estado_iva){        
        $sql = "INSERT INTO proyectos (
            numero,
            id_vendedor,
            id_cliente,
            id_grupo,
            nombre,
            valor,
            fecha_inicio,
            estado,
            estado_iva
        ) 
        VALUES 
            (?,?,?,?,?,?,?,?,?);";
        try{
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $numero,
                $id_vendedor,
                $id_cliente,
                0,                
                $trabajo,
                $valor,
                $fecha_inicio,
                $estado,
                $estado_iva
            ]);
        }catch(Exception $e) {
            die($e);
        }
    }

    private function pagos_agregar_unico($id_proyecto,$valor,$fecha){
        $res = [];    
        $sql = "INSERT INTO pagos (id_proyecto,valor,fecha) VALUES (?,?,?)";
        try{
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_proyecto,$valor,$fecha]);            
        }catch(Exception $e) {
            die($e);
        }
    }

    private function check_cliente_nombre($nombre){
        $sql = "SELECT id,nombre FROM clientes WHERE nombre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nombre]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    private function ultimo_id_cliente(){
        $sql = "SELECT id FROM clientes ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    //Actualizar estados pagos

    public function actualizar_pagos(){
        //1.Descargar todos los proyectos activos
        //2.Por cada proyecto descargar sumatoria pagos y comparar con total
        //3.Editar estado si sum = valor
        $lista = $this->lista_activos();
        $actualizados = 0;
        echo "Total lista " . count($lista) . PHP_EOL;
        for($i = 0; $i < count($lista); $i++){
            $ok = "";
            $id_proyecto = $lista[$i]["id"];
            $valor = $lista[$i]["valor"];
            $valor_iva = ceil($valor*1.19); 
            $sum = $this->sum_pagos($id_proyecto);            
            if($sum == $valor_iva){
                $ok = " ok";
                if($this->actualizar($id_proyecto)){
                    $actualizados++;
                }
            }
            echo $id_proyecto . " valor:" . $valor_iva . " " . $sum . $ok . PHP_EOL;
        }
        echo $actualizados . " actualizados" . PHP_EOL;
    }

    private function lista_activos(){
        $sql = "SELECT
            proyectos.*
        FROM proyectos
        WHERE proyectos.estado = 'ACTIVO'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    private function sum_pagos($id_proyecto){
        $sql = "SELECT SUM(valor) FROM pagos WHERE pagos.id_proyecto = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_proyecto]);
        $sum = $stmt->fetchColumn();
        return $sum;
        // if($this->check_valores($id_proyecto,$valor,$sum)){
        //     $act++;
        // }
    }

    private function actualizar($id_proyecto){
        $sql = "UPDATE proyectos SET estado_pagos = 1 WHERE proyectos.id = ?";
        $stmt = $this->pdo->prepare($sql);
        if($stmt->execute([$id_proyecto])){
            return true;
        }else{
            return false;
        }        
    }
}

?>
