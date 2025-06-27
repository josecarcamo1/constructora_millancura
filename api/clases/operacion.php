<?php
class Operacion{
    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    //Lista
    public function lista(){
        
        $res = [];    
        $sql = "SELECT
                    proyectos.*,
                    clientes.nombre AS cliente,                
                    clientes.rut AS rut
                FROM proyectos
                LEFT JOIN clientes ON clientes.id = proyectos.id_cliente
                WHERE proyectos.estado = 'ACTIVO'
                ORDER BY proyectos.numero DESC";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            die($e);
        }

        for($i = 0; $i < count($res);$i++){            
            //Agregar pagos
            $pagos = $this->pagos_proyecto($res[$i]["id"]);
            $facturas = $this->facturas_proyecto($res[$i]["id"]);
            $res[$i]["pagos"] = $pagos;
            $estado_iva = $res[$i]["estado_iva"];

            //Calculo días hábiles activos
            $dias_activo = 0;

            if($res[$i]["estado"] == "ACTIVO"){
                $hoy = time();
                $dias_activo = $hoy - strtotime($res[$i]["fecha_inicio"]);                
                $fecha_termino = "";
            }else{
                $dias_activo = strtotime($res[$i]["fecha_termino"]) - strtotime($res[$i]["fecha_inicio"]);
                $fecha_termino = new DateTimeImmutable($res[$i]["fecha_termino"]);
                $res[$i]["fecha_termino"] = $fecha_termino->format("d/m/Y");
            }

            $res[$i]["dias_activo"] = round($dias_activo / (60 * 60 * 24));

            //Calculo valor + IVA
            $valor = $res[$i]["valor"];
            $valor_iva = $valor * 1.19;
            $iva = $valor * 0.19;            

            //Calculo Pagado
            $pagado = 0;
            
            for($j = 0; $j < count($pagos); $j++){
                $pagado += $pagos[$j]["valor"];
            }

            //Calculo Facturado
            $facturado = 0;
            
            for($j = 0; $j < count($facturas); $j++){
                $facturado += $facturas[$j]["valor"];
            }

            //Formatos fechas
            $fecha_inicio = new DateTimeImmutable($res[$i]["fecha_inicio"]);
            $res[$i]["fecha_inicio"] = $fecha_inicio->format("d/m/Y");            
            
            //Agregar a respuesta
            if($estado_iva){
                $res[$i]["valor_con_iva"] = $this->peso($valor_iva);
                $res[$i]["iva"] = $this->peso($iva);
                $res[$i]["pendiente"] = $this->peso(abs($valor_iva - $pagado));
                $res[$i]["pendiente_factura"] = $this->peso(abs($valor_iva - $facturado));
            }else{
                $res[$i]["valor_con_iva"] = "N/A";
                $res[$i]["iva"] = 0;
                $res[$i]["pendiente"] = $this->peso(abs($valor - $pagado));
            }
            $res[$i]["valor"] = $this->peso($res[$i]["valor"]);
            $res[$i]["pagado"] = $this->peso($pagado);
        }

        return ["draw"=> 1,"recordsTotal"=> count($res),"recordsFiltered"=> count($res),"data"=>$res];
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

    private function facturas_proyecto($id){
        $res = [];
        $sql = "SELECT * FROM facturas WHERE facturas.id_proyecto = ? ORDER BY facturas.id DESC";
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
        $id = $_POST["id_op"];

        $sql = "SELECT 
                    proyectos.*,
                    clientes.nombre AS nombre_cliente,
                    vendedores.nombre AS vendedor
                FROM proyectos
                LEFT JOIN clientes ON clientes.id = proyectos.id_cliente
                LEFT JOIN vendedores ON vendedores.id = proyectos.id_vendedor
                WHERE proyectos.id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            $pagos = $this->pagos_proyecto($id);
            $facturas = $this->facturas_proyecto($id);
            $res["pagos"] = $pagos;
            $res["facturas"] = $facturas;
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }

    private function peso($valor){        
        $fmt = numfmt_create( 'es_CL', NumberFormatter::CURRENCY );
        return numfmt_format_currency($fmt, $valor, "CLP");
    }
}
?>