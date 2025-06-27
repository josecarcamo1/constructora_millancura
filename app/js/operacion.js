let url = "../../api/control.php";
import{rnd,ceil,calcular_dias,peso} from './_apoyo.js';

$().ready(function(){    
    $("#nav_activos").addClass("active");
    leer_sesion();

    $("#descargar").on("click",function(e){
        e.preventDefault;
        excel();
    });
});

function leer_sesion() {
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"leer_sesion"
        },
        success: function(r){
            ejecutar(JSON.parse(r));            
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function ejecutar(sess){
    console.log(sess);
    // debug_lista(sess);
    lista_proyectos(sess);
}

function debug_lista(sess){
    let vendedores = "Todos";
    if(sess.acceso == "Vendedor"){
        vendedores = sess.vendedores;
    }

    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"operacion_lista",
            vendedores:vendedores
        },
        success: function(r){
            console.log(r);
            // console.log(JSON.parse(r));
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function lista_proyectos(sess){

    let vendedores = "Todos";
    if(sess.acceso == "Vendedor"){
        vendedores = sess.vendedores;
    }

    let tabla = '<table id="lista_dt" class="table table-sm table-hover">' +
        '<thead>' +
            '<tr>' +                        
                '<th class="text-left">N°</th>' +
                '<th class="text-left">Cliente</th>' +
                '<th class="text-left">Nombre</th>' +                
                '<th class="text-left">Fecha Creación</th>' +
                '<th class="text-left">Dias</th>' + //Formato DD/MM/AAAA solo días hábiles desde inicio + 1
                '<th class="text-left">Valor</th>' +
                '<th class="text-left">IVA</th>' +
                '<th class="text-left">Valor + IVA</th>' +
                '<th class="text-left">Pagado</th>' +
                '<th class="text-left">Pendiente</th>' +
                '<th class="text-center" width="20px">Detalle</th>' +
            '</tr>' +
        '</thead>' +        
    '</table>';
    $('#lista_operaciones').append(tabla);

    $('#lista_dt').dataTable({
        "order": [],
        "processing": true,
        "responsive":true,
        "ajax": {
            "url": url,
            "type":'POST',
            "data":{
                tipo:"operacion_lista",
                vendedores:vendedores
            }  
        },
        "columns": [
            { data: 'numero' },            
            { data: 'cliente' },
            { data: 'nombre' },
            { data: 'fecha_inicio' },
            { data: 'dias_activo' },
            { data: 'valor'},
            { data: 'iva'},
            { data: 'valor_con_iva'},
            { data: 'pagado'},
            { data: 'pendiente'},
            { data: 'id',"mRender":function(data,type,full){
                if(data){
                    return '<a href="operacion_detalle.php?id=' + data + '" class="btn btn-xl text-white btn-primary"><i class="fa fa-file"></i></a>'
                }
            }},
        ],
        "language": {
            "sProcessing":     "",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "pageLength": 50
    });
}

function excel(){
    let desde = $("#desde").val();
    let hasta = $("#hasta").val();

    window.location = "../../api/excel/ventas_rango.php?desde=" + desde + "&hasta=" + hasta;

    // console.log(desde + " " + hasta);

    // $.ajax({
    //     type: "POST",
    //     url: "../../api/excel/ventas_rango.php",
    //     data:{
    //         desde:desde,
    //         hasta:hasta
    //     },
    //     success: function(r){
    //         console.log(r);
    //     },
    //     error: function(e){            
    //         console.error(e.responseText);
    //     }
    // });
}