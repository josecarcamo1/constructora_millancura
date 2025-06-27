<?php
class Reportes{

    protected PDO $pdo;
    
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }


    //General
    //Total ultimos 12
    public function proyectos_ultimos_12(){
        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_proyectos_mes(intval($meses[$i][0]),intval($meses[$i][1]));
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }
    private function total_proyectos_mes($mes,$año){
        $sql = "SELECT COUNT(proyectos.id) AS total_mes 
        FROM proyectos 
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }
    

    //Ventas ultimos 12
    public function ventas_ultimos_12(){
        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_ventas_mes(intval($meses[$i][0]),intval($meses[$i][1]));
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }
    public function ventas_ultimos_12_iva(){
        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_ventas_mes(intval($meses[$i][0]),intval($meses[$i][1]));
            $meses[$i][2] = $total["total_mes"] * 1.19;
        }

        return $meses;
    }
    private function total_ventas_mes($mes,$año){
        $sql = "SELECT SUM(proyectos.valor) AS total_mes 
        FROM proyectos
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }

    //Promedio ventas ultimos 12
    public function promedio_ventas_ultimos_12(){
        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->promedio_ventas_mes(intval($meses[$i][0]),intval($meses[$i][1]));
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }
    public function promedio_ventas_ultimos_12_iva(){
        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->promedio_ventas_mes(intval($meses[$i][0]),intval($meses[$i][1]));
            $meses[$i][2] = $total["total_mes"] * 1.19;
        }

        return $meses;
    }
    private function promedio_ventas_mes($mes,$año){
        $sql = "SELECT AVG(proyectos.valor) AS total_mes
        FROM proyectos
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }


    //Vendedores
    //Total ultimos 12
    public function proyectos_ultimos_12_vendedores(){
        $id_vendedor = $_POST["id_vendedor"];

        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_proyectos_mes_vendedores(intval($meses[$i][0]),intval($meses[$i][1]),$id_vendedor);
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }

    private function total_proyectos_mes_vendedores($mes,$año,$id_vendedor){
        $sql = "SELECT COUNT(proyectos.id) AS total_mes, vendedores.nombre AS vendedor 
        FROM proyectos 
        LEFT JOIN vendedores ON vendedores.id = proyectos.id_vendedor
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?
        AND proyectos.id_vendedor = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes,$id_vendedor]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }
    

    //Ventas ultimos 12
    public function ventas_ultimos_12_vendedores(){
        $id_vendedor = $_POST["id_vendedor"];

        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_ventas_mes_vendedores(intval($meses[$i][0]),intval($meses[$i][1]),$id_vendedor);
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }

    public function ventas_ultimos_12_vendedores_iva(){
        $id_vendedor = $_POST["id_vendedor"];

        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->total_ventas_mes_vendedores(intval($meses[$i][0]),intval($meses[$i][1]),$id_vendedor);
            $meses[$i][2] = $total["total_mes"] * 1.19;
        }

        return $meses;
    }

    private function total_ventas_mes_vendedores($mes,$año,$id_vendedor){
        $sql = "SELECT SUM(proyectos.valor) AS total_mes 
        FROM proyectos
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?
        AND proyectos.id_vendedor = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes,$id_vendedor]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }

    

    //Promedio ventas ultimos 12
    public function promedio_ventas_ultimos_12_vendedores(){
        $id_vendedor = $_POST["id_vendedor"];

        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->promedio_ventas_mes_vendedores(intval($meses[$i][0]),intval($meses[$i][1]),$id_vendedor);
            $meses[$i][2] = $total["total_mes"];
        }

        return $meses;
    }

    public function promedio_ventas_ultimos_12_vendedores_iva(){
        $id_vendedor = $_POST["id_vendedor"];

        $meses = [];
        for ($i = 1; $i <= 13; $i++) {
            $meses[0] = [date("m"),date("Y")];
            $meses[$i] = [date("m", strtotime( date( 'Y-m-01' )." -$i months")),date("Y", strtotime( date( 'Y-m-01' )." -$i months"))];
        }
        
        for($i = 0; $i < count($meses);$i++){
            $total = $this->promedio_ventas_mes_vendedores(intval($meses[$i][0]),intval($meses[$i][1]),$id_vendedor);
            $meses[$i][2] = $total["total_mes"] * 1.19;
        }

        return $meses;
    }
    
    private function promedio_ventas_mes_vendedores($mes,$año,$id_vendedor){
        $sql = "SELECT AVG(proyectos.valor) AS total_mes
        FROM proyectos
        WHERE YEAR(proyectos.fecha_inicio) = ?
        AND MONTH(proyectos.fecha_inicio) = ?
        AND proyectos.id_vendedor = ?";

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$año,$mes,$id_vendedor]);
            $res = $stmt->fetch();
            return $res;
        }catch(Exception $e) {
            die($e);
        }
    }    
}

?>