<?php
class Proyectos{

    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Agregar
    public function agregar($id_grupo,$id_cliente){
        $id_vendedor = $_POST["vendedor"];
        $nombre = $_POST["nombre"];
        $valor = $_POST["valor"];
        $fecha_inicio = $_POST["fecha_inicio"];
        $estado = "ACTIVO";
        $numero = $this->ultimo_numero() + 1;
        $estado_iva = $_POST["estado_iva"];

        $sql = "INSERT INTO proyectos (            
            id_vendedor,
            id_cliente,            
            id_grupo,
            nombre,
            numero,
            valor,
            fecha_inicio,
            estado,
            estado_iva,
            estado_pagos,
            estado_factura,
            estado_entregado
        ) 
        VALUES 
            (?,?,?,?,?,?,?,?,?,?,?,?)";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $id_vendedor,
                    $id_cliente,
                    $id_grupo,
                    $nombre,
                    $numero,
                    $valor,
                    $fecha_inicio,
                    $estado,
                    $estado_iva,
                    0,
                    0,
                    0
                ])){
                return "Pedido Agregado";
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    private function ultimo_numero(){
        $res = [];    
        $sql = "SELECT numero FROM proyectos ORDER BY numero DESC LIMIT 1";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchColumn();
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Editar
    public function editar($id_cliente){
        $id = $_POST["id_venta"];
        $nombre = $_POST["nombre"];
        $id_vendedor = $_POST["vendedor"];
        $valor = $_POST["valor"];
        $fecha = $_POST["fecha"];
        $entregado = $_POST["entregado"];
        $estado_iva = $_POST["iva"];
          
        $sql = "UPDATE proyectos SET 
            nombre = ?,
            estado_iva = ?,
            estado_entregado = ?,
            id_vendedor = ?,
            id_cliente = ?,
            valor = ?,
            fecha_inicio = ?
        WHERE id = ?;";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([
                    $nombre,
                    $estado_iva,
                    $entregado,
                    $id_vendedor,
                    $id_cliente,
                    $valor,
                    $fecha,
                    $id,
                ])){
                    if($this->check_estado($id)){
                        return "Venta Cerrada";
                    }else{
                        return "Pedido Editado";
                    }
            }else{
                return $stmt->errorInfo();
            }
        }catch(Exception $e) {
            die($e);
        }
        
    }

    //Lista
    // public function lista(){
        
    //     $res = [];    
    //     $sql = "SELECT
    //                 proyectos.*,
    //                 clientes.nombre AS cliente,                
    //                 clientes.rut AS rut
    //             FROM proyectos
    //             LEFT JOIN clientes ON clientes.id = proyectos.id_cliente";
    //     try{
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute();
    //         $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     }catch(Exception $e) {
    //         die($e);
    //     }

    //     for($i = 0; $i < count($res);$i++){

    //         //Agregar pagos
    //         $pagos = $this->pagos_proyecto($res[$i]["id"]);
    //         $res[$i]["pagos"] = $pagos;

    //         //Calculo días hábiles activos
    //         $dias_activo = 0;

    //         if($res[$i]["estado"] == "ACTIVO"){
    //             $hoy = time();
    //             $dias_activo = $hoy - strtotime($res[$i]["fecha_inicio"]);                
    //             $fecha_termino = "";
    //         }else{
    //             $dias_activo = strtotime($res[$i]["fecha_termino"]) - strtotime($res[$i]["fecha_inicio"]);
    //             $fecha_termino = new DateTimeImmutable($res[$i]["fecha_termino"]);
    //             $res[$i]["fecha_termino"] = $fecha_termino->format("d/m/Y");
    //         }
    //         $res[$i]["dias_activo"] = round($dias_activo / (60 * 60 * 24));

    //         //Calculo valor + IVA
    //         $valor_iva = $res[$i]["valor"] * 1.19;
    //         $iva = $res[$i]["valor"] * 0.19;

    //         //Calculo Pagado
    //         $pagado = 0;
            
    //         for($j = 0; $j < count($pagos); $j++){
    //             $pagado += $pagos[$j]["valor"];
    //         }

    //         //Formatos fechas
    //         $fecha_inicio = new DateTimeImmutable($res[$i]["fecha_inicio"]);            
    //         $res[$i]["fecha_inicio"] = $fecha_inicio->format("d/m/Y");
            
    //         //Agregar a respuesta
    //         $res[$i]["iva"] = $iva;
    //         $res[$i]["valor_iva"] = $valor_iva;
    //         $res[$i]["pagado"] = $pagado;
    //         $res[$i]["pendiente"] = $valor_iva - $pagado;
    //     }

    //     return ["draw"=> 1,"recordsTotal"=> count($res),"recordsFiltered"=> count($res),"data"=>$res];
    // }

    public function lista(){
        $res = [];
        $sql = "
            SELECT
                p.*,
                c.nombre AS cliente,
                c.rut AS rut,
                COALESCE(SUM(pg.valor), 0) AS total_pagado,
                (p.valor * 0.19) AS iva,
                (p.valor * 1.19) AS valor_iva,
                CASE 
                    WHEN p.estado = 'ACTIVO' THEN DATEDIFF(CURDATE()+1, p.fecha_inicio)
                    ELSE DATEDIFF(p.fecha_termino, p.fecha_inicio)
                END AS dias_activo,
                CASE 
                    WHEN p.estado = 'ACTIVO' THEN NULL
                    ELSE DATE_FORMAT(p.fecha_termino, '%d/%m/%Y')
                END AS formatted_fecha_termino,
                DATE_FORMAT(p.fecha_inicio, '%d/%m/%Y') AS formatted_fecha_inicio
            FROM proyectos p
            LEFT JOIN clientes c ON c.id = p.id_cliente
            LEFT JOIN pagos pg ON pg.id_proyecto = p.id
            GROUP BY p.id, c.nombre, c.rut
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e);
        }

        // Post-process results to include additional calculations
        foreach ($res as &$row) {
            $row["pendiente"] = $row["valor_iva"] - $row["total_pagado"];
            $row["fecha_inicio"] = $row["formatted_fecha_inicio"];
            unset($row["formatted_fecha_inicio"]);
            if ($row["estado"] !== "ACTIVO") {
                $row["fecha_termino"] = $row["formatted_fecha_termino"];
            }
            unset($row["formatted_fecha_termino"]);
        }

        return [
            "draw" => 1,
            "recordsTotal" => count($res),
            "recordsFiltered" => count($res),
            "data" => $res,
        ];
    }


    private function pagos_proyecto($id){
        $res = [];
        $sql = "SELECT * FROM pagos WHERE pagos.id_proyecto = ? ORDER BY pagos.id DESC";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }
        return $res;
    }

    //Info
    public function info(){
        $id = $_POST["id"];
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            $results = $stmt->fetch();
            return $results;
        }catch(Exception $e) {
            die($e);
        }
    }

    //Borrar
    public function borrar(){
        $id = $_POST["id"];
        try{
            $stmt = $this->pdo->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            return "Turno Borrado";
        }catch(Exception $e) {
            die($e);
        }
    }
    
    public function editar_estado($estado_proyecto,$id_proyecto){
        $sql = "UPDATE proyectos SET             
            estado = ?
        WHERE id = ?;";
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([$estado_proyecto,$id_proyecto])){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e) {
            die($e);
        }
    }

    public function check_iva($id_proyecto){
        $sql = "SELECT estado_iva FROM proyectos WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_proyecto]);
            $res = $stmt->fetchColumn();
            return $res;
        }catch(Exception $e){
            die($e);
        }
    }

    public function actualizar_estados($tipo_estado,$valor,$id_proyecto){
        if($tipo_estado == "pagos"){
            $sql = "UPDATE proyectos SET estado_pagos = ? WHERE id = ?";
        }else if($tipo_estado == "facturas"){
            $sql = "UPDATE proyectos SET estado_factura = ? WHERE id = ?";
        }else if($tipo_estado == "entregado"){
            $sql = "UPDATE proyectos SET estado_entregado = ? WHERE id = ?";
        }
        
        try{
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute([$valor,$id_proyecto])){
                return $this->check_estado($id_proyecto);
            }else{
                return false;
            }
        }catch(Exception $e) {
            die($e);
        }        
    }

    public function check_estado($id_proyecto){
        $iva = $this->check_iva($id_proyecto);
        $pagos = $this->check_pagos($id_proyecto);
        $factura = $this->check_factura($id_proyecto);
        $entregado = $this->check_entregado($id_proyecto);

        if($iva){
            if($pagos && $factura && $entregado){
                if($this->cerrar($id_proyecto)){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            if($pagos && $entregado){
                if($this->cerrar($id_proyecto)){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

    public function check_pagos($id_proyecto){
        $sql = "SELECT estado_pagos FROM proyectos WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_proyecto]);
            $res = $stmt->fetchColumn();
            return $res;
        }catch(Exception $e){
            die($e);
        }
    }

    public function check_factura($id_proyecto){
        $sql = "SELECT estado_factura FROM proyectos WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_proyecto]);
            $res = $stmt->fetchColumn();
            return $res;
        }catch(Exception $e){
            die($e);
        }
    }

    public function check_entregado($id_proyecto){
        $sql = "SELECT estado_entregado FROM proyectos WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_proyecto]);
            $res = $stmt->fetchColumn();
            return $res;
        }catch(Exception $e){
            die($e);
        }
    }

    public function cerrar($id_proyecto){
        $sql = "UPDATE proyectos SET estado = 'TERMINADO', fecha_termino = ? WHERE id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([date('Y-m-d'),$id_proyecto]);
        }catch(Exception $e){
            die($e);
        }
    }
}
?>