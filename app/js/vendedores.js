let url = "../../api/control.php";
import{rnd,ceil,calcular_dias,peso} from './_apoyo.js';

$().ready(function(){
    lista_debug();
    lista_vendedores();
    $("#nav_admin").addClass("active");
});

function lista_debug(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"vendedores_lista"
        },
        success: function(r){       
            let res = JSON.parse(r);
            console.log(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function lista_vendedores(){
    let tabla = '<table id="lista_dt" class="table table-sm table-hover">' +
        '<thead>' +
            '<tr>' +
                '<th class="text-left">Color</th>' +
                '<th class="text-left">Estado</th>' +
                '<th class="text-left">Nombre</th>' +                
                '<th class="text-center" width="20px">Detalle</th>' +
            '</tr>' +
        '</thead>' +        
    '</table>';
    $('#lista_vendedores').append(tabla);

    $('#lista_dt').dataTable({
        "order": [],
        "processing": true,
        "responsive":true,
        "ajax": {
            "url": url,
            "type":'POST',
            "data":{
                tipo:"vendedores_lista"
            }  
        },
        "columns": [
            { data: 'color',"mRender":function(data,type,full){
                if(data){
                    return '<input type="color" class="form-control form-control-color" id="exampleColorInput" value="' + data +'" title="Color" disabled>'
                }
            }},
            { data: 'estado' },
            { data: 'nombre' },                        
            { data: 'id',"mRender":function(data,type,full){
                if(data){
                    return '<a href="vendedor_detalle.php?id=' + data + '" class="btn btn-xl text-white btn-primary"><i class="fa fa-file"></i></a>'
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