<?php
ini_set('html_errors', false);
include "config.php";

include "clases/jwt_ver.php";
include "clases/sesiones.php";
include "clases/carga.php";
include "clases/clientes.php";
include "clases/proyectos.php";
include "clases/grupos.php";
include "clases/vendedores.php";
include "clases/operacion.php";
include "clases/pagos.php";
include "clases/facturas.php";
include "clases/regiones_comunas.php";
include "clases/reportes.php";
include "clases/usuarios.php";
include "clases/correos.php";

$jwt_ver = new JwtVer($key);
$sesiones = new Sesiones($pdo);
$carga = new Carga($pdo);
$clientes = new Clientes($pdo);
$proyectos = new Proyectos($pdo);
$grupos = new Grupos($pdo);
$vendedores = new Vendedores($pdo);
$operacion = new Operacion($pdo);
$pagos = new Pagos($pdo);
$facturas = new Facturas($pdo);
$reportes = new Reportes($pdo);
$usuarios = new Usuarios($pdo,$cred);
$reg_com = new Regiones_comunas($pdo);
$correos = new Correos($pdo,$cred);

$tipo = $_POST["tipo"];
$ver = $jwt_ver->verificar();

if($ver == "Ok"){
    /////////////////////////////////////////////////////////////Sesiones
    if($tipo == "check_sess"){    
        echo $sesiones->check_sess();
    }

    if($tipo == "leer_sesion"){    
        echo $sesiones->leer_sesion();
    }

    if($tipo == "crear_sesiones"){
        echo $sesiones->crear();
    }

    if($tipo == "cambiar_sesion"){
        echo $sesiones->cambiar();
    }

    //////////////////////////////////////////////////////////////Carga
    if($tipo == "carga_clientes"){    
        echo $carga->clientes();
    }

    if($tipo == "carga_proyectos"){    
        echo $carga->proyectos();
    }

    if($tipo == "actualizar_pagos"){    
        echo $carga->actualizar_pagos();
    }
    
    //////////////////////////////////////////////////////////////Clientes
    if($tipo == "clientes_lista"){    
        echo json_encode($clientes->lista());
    }

    if($tipo == "clientes_agregar"){    
        echo $clientes->agregar();
    }

    if($tipo == "clientes_agregar_basico"){    
        echo $clientes->agregar_basico();
    }

    if($tipo == "clientes_info"){
        echo json_encode($clientes->info());
    }

    if($tipo == "clientes_editar"){    
        echo $clientes->editar();
    }

    if($tipo == "clientes_borrar"){    
        echo $clientes->borrar();
    }

    ////////////////////////////////////////////////////////////////Proyectos
    if($tipo == "proyectos_lista"){
        echo json_encode($proyectos->lista());
    }

    if($tipo == "proyecto_agregar"){
        $cantidad = $_POST["cantidad"];
        $cliente = $_POST["cliente"];
        $id_cliente = 0;

        if(is_numeric($cliente)){
            $id_cliente = $cliente;
        }else{
            $id_cliente = $clientes->agregar_basico($cliente);
        }

        if($cantidad > 1){
            $grupos->agregar();
            $ultimo_id_grupo = $grupos->ultimo_id();
            for($i = 0; $i < $cantidad; $i++){
                $proyectos->agregar($ultimo_id_grupo,$id_cliente);
            }
            echo "Grupo y proyectos creados";
        }else{
            $proyectos->agregar(0,$id_cliente);
            echo "Proyecto Agregado";
        }        
    }

    if($tipo == "proyecto_info"){
        echo json_encode($proyectos->info());
    }

    if($tipo == "proyecto_editar"){
        $cliente = $_POST["cliente"];        
        $id_cliente = 0;

        if(is_numeric($cliente)){
            $id_cliente = $cliente;
        }else{
            $id_cliente = $clientes->agregar_basico($cliente);
        }
        
        echo $proyectos->editar($id_cliente);
    }

    if($tipo == "proyecto_check_iva"){        
        return $proyectos->check_iva();
    }

    if($tipo == "proyecto_check_estados"){
        return $proyectos->check_estados();
    }   

    ////////////////////////////////////////////////////////////////Operación
    if($tipo == "operacion_lista"){
        echo json_encode($operacion->lista());
    }

    if($tipo == "operacion_info"){
        echo json_encode($operacion->info());
    }

    /////////////////////////////////////////////////////////////////Vendedores
    if($tipo == "vendedores_lista"){
        echo json_encode($vendedores->lista());
    }

    if($tipo == "vendedores_lista_selector"){
        echo json_encode($vendedores->lista_selector());
    }

    if($tipo == "vendedores_editar"){    
        echo $vendedores->editar();
    }

    if($tipo == "vendedores_nuevo"){
        echo $vendedores->agregar();
    }

    if($tipo == "vendedores_info"){
        echo json_encode($vendedores->info());
    }
    

    /////////////////////////////////////////////////////////////////Pagos
    if($tipo == "pagos_nuevo"){
        if($pagos->nuevo()){
            $estado_pago = $_POST["estado_pago"];
            $id_proyecto = $_POST["id_proyecto"];
            $tipo_estado = "pagos";
            if($estado_pago == "PAGADO"){
                $valor = 1;
                if($proyectos->actualizar_estados($tipo_estado,$valor,$id_proyecto)){
                    echo "Venta Cerrada";
                }                 
            }else{
                echo "Pago agregado";
            }
        }else{
            echo "Error al crear el pago.";
        }
    }

    if($tipo == "pagos_borrar"){
        echo $pagos->borrar();
    }

    /////////////////////////////////////////////////////////////////Facturas
    if($tipo == "facturas_nueva"){
        if($facturas->nueva()){
            $estado_factura = $_POST["estado_factura"];
            $id_proyecto = $_POST["id_proyecto"];
            $tipo_estado = "facturas";
            if($estado_factura == "FACTURADO"){
                $valor = 1;
                if($proyectos->actualizar_estados($tipo_estado,$valor,$id_proyecto)){
                    echo "Venta Cerrada";
                }
            }else{
                echo "Factura agregada";
            }
        }else{
            echo "Error al crear factura";
        }
    }

    if($tipo == "facturas_lista"){
        echo $facturas->lista();
    }

    if($tipo == "facturas_lista_id_proyecto"){
        echo $facturas->lista_id_proyecto();
    }

    if($tipo == "facturas_borrar"){
        echo $facturas->borrar();
    }

    /////////////////////////////////////////////////////////////////Regiones Comunas
    if($tipo == "lista_comunas"){
        echo json_encode($reg_com->lista_comunas());
    }

    if($tipo == "prov_reg"){
        $id_provincia = $_POST["id_provincia"];
        echo json_encode($reg_com->prov_reg($id_provincia));
    }

    //////////////////////////////////////////////////////////////////Reportes

    //General
    if($tipo == "reportes_total_ultimos_12"){
        echo json_encode($reportes->proyectos_ultimos_12());
    }

    if($tipo == "reportes_ventas_ultimos_12"){
        echo json_encode($reportes->ventas_ultimos_12());
    }

    if($tipo == "reportes_ventas_ultimos_12_iva"){
        echo json_encode($reportes->ventas_ultimos_12_iva());
    }

    if($tipo == "reportes_promedio_ventas_ultimos_12"){
        echo json_encode($reportes->promedio_ventas_ultimos_12());
    }

    if($tipo == "reportes_promedio_ventas_ultimos_12_iva"){
        echo json_encode($reportes->promedio_ventas_ultimos_12_iva());
    }

    //Vendedores
    if($tipo == "reportes_total_ultimos_12_vendedores"){
        echo json_encode($reportes->proyectos_ultimos_12_vendedores());
    }

    if($tipo == "reportes_ventas_ultimos_12_vendedores"){
        echo json_encode($reportes->ventas_ultimos_12_vendedores());
    }

    if($tipo == "reportes_ventas_ultimos_12_vendedores_iva"){
        echo json_encode($reportes->ventas_ultimos_12_vendedores_iva());
    }

    if($tipo == "reportes_promedio_ventas_ultimos_12_vendedores"){
        echo json_encode($reportes->promedio_ventas_ultimos_12_vendedores());
    }

    if($tipo == "reportes_promedio_ventas_ultimos_12_vendedores_iva"){
        echo json_encode($reportes->promedio_ventas_ultimos_12_vendedores_iva());
    }

    //Usuarios
    if($tipo == "usuarios_lista"){
        echo json_encode($usuarios->lista());
    }

    if($tipo == "usuarios_info"){
        $id = $_POST["id"];
        echo json_encode($usuarios->info($id));
    }

    if($tipo == "usuarios_borrar"){
        $id = $_POST["id"];
        echo $usuarios->borrar($id);
    }

    if($tipo == "usuarios_agregar"){
        $nombre = $_POST["nombre"];
        $correo = $_POST["correo"];
        $acceso = $_POST["acceso"];
        echo $usuarios->agregar($nombre,$correo,$acceso);
    }

    if($tipo == "usuarios_actualizar"){
        $id = $_POST["id"];
        $nombre = $_POST["nombre"];
        $estado = $_POST["estado"];
        $acceso = $_POST["acceso"];
        echo $usuarios->actualizar($id,$nombre,$estado,$acceso);
    }

    if($tipo == "usuarios_cambiar_contr"){
        $id = $_POST["id"];
        $correo = $_POST["usuario"];
        $pass = $_POST["contr"];
        $pass_nuevo = $_POST["contr_nueva"];

        // echo json_encode($usuarios->verificar_contr($correo,$pass,$id));

        if($usuarios->verificar_contr($correo,$pass,$id)){
            echo $usuarios->cambiar_contr($id,$correo,$pass_nuevo);
        }else{
            echo "La contraseña no coincide";
        }
    }

    if($tipo == "usuarios_agregar_vendedor"){
        $id_usuario = $_POST["id_usuario"];
        $id_vendedor = $_POST["id_vendedor"];
        echo $usuarios->agregar_vendedor($id_usuario,$id_vendedor);
    }

    if($tipo == "usuarios_borrar_vendedor"){
        $id = $_POST["id"];
        echo $usuarios->borrar_vendedor($id);
    }

    if($tipo == "usuarios_lista_vendedores"){
        $id_usuario = $_POST["id_usuario"];
        echo json_encode($usuarios->lista_vendedores_usuario($id_usuario));
    }

    ////////////////////////////////////////////////////////////Correos
    if($tipo == "correos_prueba"){
        echo json_encode($correos->prueba());
    }

    ////////////////////////////////////////////////////////////Excel
    if($tipo == "excel_ventas_rango"){
        echo json_encode($excel->ventas_rango());
    }
}else{
    echo json_encode(["error"=>$ver]);
}
?>