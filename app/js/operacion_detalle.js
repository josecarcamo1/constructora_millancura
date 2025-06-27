let url = "../../api/control.php";
import{rnd,calcular_dias,peso,format_fecha,ceil} from './_apoyo.js';
let searchParams = new URLSearchParams(window.location.search);
let id_op = searchParams.get("id");

$().ready(function(){
    $("#nav_activos").addClass("active");
    info();

    //Quita spinner por defecto
    $("#spinner").hide();

    //Fechas
    fecha_creacion_pago.max = moment();
    document.getElementById('fecha_creacion_pago').valueAsDate = new Date();

    fecha_creacion_factura.max = moment();
    document.getElementById('fecha_creacion_factura').valueAsDate = new Date();
});

function info(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
           tipo:"operacion_info",
           id_op:id_op
        },
        success: function(r){
            let res = JSON.parse(r);
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        let pago = 0;
        let pend = 0;
        let pend_fact = 0;
        let valor = r.valor;
        let valor_iva = 0;
        let dias_activo = 0;
        let facturado = 0;
        let estado_pago = "";
        let estado_factura = "";
        let iva = "";

        //Calculo total pagos
        if(r.pagos){
            for(let i = 0; i < r.pagos.length; i++){                
                pago += r.pagos[i].valor;
            }
        }

        //Calculo total facturas
        if(r.facturas){
            for(let i = 0; i < r.facturas.length; i++){
                facturado += r.facturas[i].valor;
            }
        }

        if(r.estado == "ACTIVO"){
            dias_activo = moment().diff(r.fecha_inicio,"days");
        }else{
            dias_activo = moment(r.fecha_termino).diff(r.fecha_inicio,"days");
        }
        
        if(!r.estado_iva){
            $("#valor_iva").val("N/A");
            estado_factura = "N/A";
            pend = valor - pago;
            iva = "SIN";            
        }else{
            valor_iva = Math.ceil(valor + (valor*0.19));
            pend = valor_iva - pago;
            $("#valor_iva").val(peso(valor_iva));
            $("#valor_iva").attr("data-valor",valor_iva);
            $("#valor_iva").attr('title', "50%: " + ceil(valor_iva/2));
            estado_factura = (r.estado_factura == 0)?"PENDIENTE":"FACTURADO"
            pend_fact = valor_iva - facturado
            iva = "CON";
            $("#facturas").show();
        }
        
        $("#numero").val(r.numero);
        $("#nombre").val(r.nombre);
        $("#estado").val(r.estado);
        $("#iva").val(iva);
        $("#vendedor").val(r.vendedor);
        $("#dias_activo").val(dias_activo);
        $("#valor").val(peso(valor));
        $("#valor").attr("data-valor",valor);
        $("#fecha_creacion").val(r.fecha_inicio);
        $("#total_pagado").val(peso(pago));
        $("#total_pagado").attr("data-valor",pago);
        $("#por_pagar").val(peso(ceil(pend)));
        $("#por_pagar").attr("data-valor",pend);
        
        //Estados
        estado_pago = (r.estado_pagos == 0)?"PENDIENTE":"PAGADO"
        $("#estado_pago").val(estado_pago);        
        $("#estado_factura").val(estado_factura);        
        $("#estado_entregado option[value='" + r.estado_entregado + "']").attr("selected","selected");

        //Listas
        lista_clientes(r.id_cliente);
        lista_vendedores(r.id_vendedor);
        
        //Nuevo Pago
        $("#ref_valor_iva").val(valor_iva);
        $("#ref_por_pagar").val(ceil(pend));
        $("#nuevo_pago").on("click",function(e){
            e.preventDefault();
            nuevo_pago();
        }); 

        //Nueva Factura
        $("#ref_fact_valor_iva").val(valor_iva);
        $("#ref_fact_por_pagar").val(ceil(pend_fact));
        $("#nueva_factura").on("click",function(e){
            e.preventDefault();
            nueva_factura(facturado,valor_iva);
        });
        
        //Lista de pagos
        pagos(r.pagos);

        //Lista facturas
        facturas(r.facturas);

        //Editar
        $('#editar, #cancelar').on('click', function(){            
            var $form = $(this).closest('form');
            $form.toggleClass('lectura editar');            
            var isReadonly = $form.hasClass('lectura');                
            let items = $form.find('input,textarea,select');            
            items.each(function(e){            
                if(!$(this).hasClass("fijo")){
                    $(this).prop('disabled', isReadonly);
                }                
            })
            $("#valor").val($("#valor").attr("data-valor"));
            $("#spinner").hide();
        });

        $("#cancelar").on("click",function(){
            location.reload();
        });

        //Editar
        $('#guardar').on('click',function(e){
            e.preventDefault();
            actualizar();
        });
    }

    function pagos(pagos){        
        for(let i = 0; i < pagos.length; i++){
            let item = '<tr>' +                
                '<td class="text-left align-middle">'+ format_fecha(pagos[i].fecha) +'</td>' +
                '<td class="text-left align-middle">'+ peso(pagos[i].valor) +'</td>' +
                '<td class="text-left align-middle  hide-mobile">'+ pagos[i].comentario +'</td>' +
                '<td class="text-center"><button id="borrar_' + pagos[i].id + '" class="btn btn-xl text-white btn-danger"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
            $("#lista_pagos").append(item);

            $("#borrar_" + pagos[i].id).on("click",function(e){
                e.preventDefault();
                borrar_pago(pagos[i].id);
            });
        }
    }

    function facturas(facturas){        
        for(let i = 0; i < facturas.length; i++){
            let item = '<tr>' +                
                '<td class="text-left align-middle">'+ format_fecha(facturas[i].fecha) +'</td>' +
                '<td class="text-left align-middle">'+ peso(facturas[i].valor) +'</td>' +
                '<td class="text-left align-middle  hide-mobile">'+ facturas[i].comentario +'</td>' +
                '<td class="text-center"><button id="borrar_' + facturas[i].id + '" class="btn btn-xl text-white btn-danger"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
            $("#lista_facturas").append(item);

            $("#borrar_" + facturas[i].id).on("click",function(e){
                e.preventDefault();
                borrar_factura(facturas[i].id);
            });
        }
    }
}

function actualizar(){
    let nombre = $("#nombre").val();    
    let vendedor = $("#lista_vendedores").val();
    let valor = $("#valor").val().trim();
    let fecha = $("#fecha_creacion").val();    
    let cliente = $("#clientes option[value='" + $("#tr").val() + "']").attr("data-id");
    let entregado = $("#estado_entregado").val();
    let iva = $("#iva").val();
    if(iva == "SIN"){
        iva = 0;
    }else{
        iva = 1;
    }

    let data = {
        tipo:"proyecto_editar",
        id_venta:id_op,
        iva:iva,
        nombre:nombre,
        vendedor:vendedor,
        valor:valor,
        fecha:fecha,
        cliente:cliente,
        entregado:entregado
    }

    if(isNaN(parseInt(cliente))){        
        data.cliente = $("#tr").val();
    }

    console.log(data);

    const errores = [];

    if(data.cliente == ""){
        errores.push("Debe ingresar un cliente");
    }

    if (errores.length > 0) {
        alert("Por favor corrija los siguientes errores:\n" + errores.join("\n"));
        return;
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        beforeSend: function(){
            $("#spinner").show();
            $(".emitir").attr("disabled", true);
        },
        success: function(r){
            alert(r);
            location.reload(true);
        },
        error: function(e){
            console.error(e.responseText);
        }
    });
}

function nuevo_pago(){
    let por_pagar = parseInt($("#por_pagar").attr("data-valor"));
    let fecha = $("#fecha_creacion_pago").val();
    let valor = parseInt($("#nuevo_valor").val());
    let comentario = $("#comentario").val();
    let estado_pago = "PENDIENTE";

    let data = {
        tipo:"pagos_nuevo",
        id_proyecto:id_op,
        valor:valor,
        comentario:comentario,
        fecha:fecha,
        estado_pago:estado_pago
    }

    console.log(por_pagar);

    if(!data.valor){
        alert ("Valor inválido");
    }else if(data.valor > por_pagar){
        alert ("El valor supera el total por pagar");
    }else{
        if(valor === por_pagar){
            data.estado_pago = "PAGADO";
        }
        $.ajax({
            type: "POST",
            url: url,
            data:data,
            success: function(r){
                // console.log(r);
                alert(r);
                location.reload();
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    }    
}

function borrar_pago(id){
    if(confirm("Desea borrar el pago?")){
        $.ajax({
            type: "POST",
            url: url,
            data:{
                tipo:"pagos_borrar",
                id:id            
            },     
            success: function(r){
                // alert(r);
                location.reload();
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    } 
}

function nueva_factura(facturado,valor_iva){
    let numero = parseInt($("#nueva_factura_numero").val());
    if(!numero){
        numero = 0;
    }
    let valor = parseInt($("#nueva_factura_valor").val());
    let comentario = $("#factura_comentario").val();    
    let fecha = $("#fecha_creacion_factura").val();
    let estado_factura = "PENDIENTE";

    if((valor + facturado) == valor_iva){
        estado_factura = "FACTURADO";
    }   

    let data = {
        tipo:"facturas_nueva",
        id_proyecto:id_op,
        numero:numero,
        valor:valor,
        comentario:comentario,
        estado_factura:estado_factura,
        fecha:fecha
    }

    console.log(data);

    if(!data.valor){
        alert ("Valor inválido");
    }else if(data.valor > valor_iva){
        alert ("El valor supera el total por pagar");
    }else{
        $.ajax({
            type: "POST",
            url: url,
            data:data,
            success: function(r){
                alert(r);
                location.reload();
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    }    
}

function borrar_factura(id){
    if(confirm("Desea borrar la factura?")){
        $.ajax({
            type: "POST",
            url: url,
            data:{
                tipo:"facturas_borrar",
                id:id            
            },     
            success: function(r){
                // alert(r);
                location.reload();
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    } 
}

function lista_clientes(id_cliente) {    
    let data = {
        tipo: "clientes_lista"
    };

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(r) {
            let res = JSON.parse(r);
            populateClientes(res, id_cliente);
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });

    function populateClientes(clientes, id_cliente) {
        $("#clientes").empty(); // Clear existing options

        let selectedClientName = "";

        for (let i = 0; i < clientes.length; i++) {
            let cliente = clientes[i];
            let item = `<option data-id="${cliente.id}" value="${cliente.nombre}"></option>`;
            $("#clientes").append(item);

            // Check if this client matches the id_cliente
            if (cliente.id == id_cliente) {
                selectedClientName = cliente.nombre;
            }
        }

        // Set the input value to the client's name if found
        if (selectedClientName) {
            $("#tr").val(selectedClientName);
        } else {
            $("#tr").val(""); // Clear the input if no match is found
        }
    }
}

function lista_vendedores(id_vendedor){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"vendedores_lista_selector"
        },
        success: function(res){
            let r = JSON.parse(res);
            render(r);
            $("#lista_vendedores option[value='" + id_vendedor + "']").attr("selected","selected");
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        for(let i = 0; i < r.length; i++){
            if(r[i].estado != "0"){
                let item = '<option value="' + r[i].id + '">' + r[i].nombre + '</option';
                $("#lista_vendedores").append(item);
            }
        }
    }
}