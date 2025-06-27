let url = "../../api/control.php";
import{rnd,ceil,calcular_dias,peso} from './_apoyo.js';

$().ready(function(){
    // lista_debug();
    lista_proyectos();
    $("#nav_admin").addClass("active");
});

function lista_debug(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"proyectos_lista"
        },
        success: function(r){       
            console.log(r);
            let res = JSON.parse(r);
            console.log(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function lista_proyectos(){
    let tabla = '<table id="lista_dt" class="table table-sm table-hover">' +
        '<thead>' +
            '<tr>' +                        
                '<th class="text-left">N°</th>' +                
                '<th class="text-left">Nombre</th>' +
                '<th class="text-left">Cliente</th>' +
                '<th class="text-left">Fecha Creación</th>' +
                '<th class="text-left">Fecha Término</th>' +
                '<th class="text-left">Dias hábiles</th>' + //Formato DD/MM/AAAA solo días hábiles desde inicio + 1
                '<th class="text-left">Estado</th>' +
                '<th class="text-left">Valor</th>' +
                '<th class="text-left">IVA</th>' +
                '<th class="text-left">Valor + IVA</th>' +
                '<th class="text-center" width="20px">Detalle</th>' +
            '</tr>' +
        '</thead>' +        
    '</table>';
    $('#lista_proyectos').append(tabla);

    $('#lista_dt').dataTable({
        "order": [],
        "processing": true,
        "responsive":true,
        "ajax": {
            "url": url,
            "type":'POST',
            "data":{
                tipo:"proyectos_lista"
            }  
        },
        "columns": [
            { data: 'numero' },            
            { data: 'nombre' },
            { data: 'cliente' },
            { data: 'fecha_inicio' },
            { data: 'fecha_termino' },
            { data: 'dias_activo' },
            { data: 'estado' },
            { data: 'valor' ,"mRender": function(data, type, full) {
                return peso(ceil(data));
            }},
            { data: 'iva' ,"mRender": function(data, type, full) {
                return peso(ceil(data));
            }}, 
            { data: 'valor_iva' ,"mRender": function(data, type, full) {
                return peso(ceil(data));
            }},             
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