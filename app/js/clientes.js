let url = "../../api/control.php";

$().ready(function(){
    lista_proyectos();
});

function lista_proyectos(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"clientes_lista"
        },
        success: function(r){       
            console.log(r);
            let res = JSON.parse(r);
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        console.log(r);
        for(let i = 0; i < r.length; i++){
            let telefono = (r[i].telefono)?r[i].telefono:"-";
            let mail = (r[i].mail)?r[i].mail:"-";
            let rut = (r[i].rut)?r[i].rut:"-";

            let item = '<tr>' +
                '<td>' + r[i].nombre +'</td>' +
                '<td>' + rut +'</td>' +
                '<td>' + telefono +'</td>' +
                '<td>' + mail + '</td>' +
                '<td>' + r[i].total + '</td>' +
                '<td class="text-center"><a href="clientes_detalle.php?id=' + r[i].id + '" class="btn btn-sm btn-primary" title="Detalle Cliente"><i class="fa fa-file"></i></a></td>' +
            '</tr> ';
            $("#lista_proyectos").append(item);
        }

        $("#tabla_proyectos").DataTable({
            "processing": true,
            "responsive":true,
            "columnDefs": [{
                "searchable": false, 
                "targets": 3
            },{
                "orderable":false,                
                "targets":[]
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

function agregar_proyecto(){

}