let url = "../../api/control.php";

$(document).ready(function(){
    lista();

    $("#correo_prueba").on("click",function(e){
        e.preventDefault();
        correo_prueba();
    });
});

//Lista
function lista(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"usuarios_lista"        
        },
        success: function(r){
            console.log(r);
            let res = JSON.parse(r);
            gen_lista(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function gen_lista(r){
        console.log(r);
        for(let i = 0; i < r.length; i++){
            
            let num = i + 1;
            let estado = (r[i].estado ? "Activo":"Inactivo");            

            let item = '<tr id=' + r[i].id +'>' +                    
                '<td class="text-center align-middle hide-mobile">'+ num +'</td>' +
                '<td class="text-left align-middle hide-mobile">'+ r[i].nombre +'</td>' +
                '<td class="text-left align-middle hide-mobile">'+ r[i].usuario +'</td>' +
                '<td class="text-center align-middle hide-mobile">'+ estado +'</td>' +
                '<td class="text-center align-middle hide-mobile">'+ r[i].acceso +'</td>' +
                '<td class="text-center align-middle"><a href="usuarios_info.php?id=' + r[i].id + '" class="btn btn-xl text-white btn-primary"><i class="fa fa-file"></i></a></td>' +
            '</tr>';
            
            $("#lista_items").append(item);
        }
        
        $('#lista_dt').dataTable( {            
            "columnDefs": [
                { 
                    // "searchable": false,                     
                    // "targets": 4 
                }
            ],
            "language": {
                "sProcessing":     "Procesando...",
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
            "pageLength": 25
        });
    }
}

function correo_prueba(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"correos_prueba"
        },
        success: function(r){
            alert(r);
            location.reload();
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}