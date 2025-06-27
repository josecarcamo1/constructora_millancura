let url = "../../api/control.php";

$().ready(function() {
    $("#agregar_proyectos").on("submit",function(e){
        e.preventDefault();
        verificarCsv($('#lista_proyectos')[0].files[0],subir_proyectos);
    });

    $("#act").on("click",function(e){
        e.preventDefault();
        actualizar_pagos();
    });
});

function verificarCsv(csv,callback){
    Papa.parse(csv, {
        download: true,
        complete: function(lista) {            
            callback(lista.data);
        }
    });
}

function subir_clientes(lista){
    console.log(lista);
    lista = JSON.stringify(lista);

    let data = {
        tipo:"carga_clientes",
        lista:lista
    };

    $.ajax({
        type: "POST",
        url:url,
        data:data,
        success: function(r){
            console.log(r);
        },
        error: function(e){
            console.error(e.responseText);
        }
    });
}

function subir_proyectos(lista){
    //Formato [#,FECHA,CLIENTE,ID_CLIENTE,TRABAJO,VENDEDOR,ID_VENDEDOR,VALOR]
    // console.log(lista);

    lista = JSON.stringify(lista);

    let data = {
        tipo:"carga_proyectos",
        lista:lista
    };

    // console.log(data);

    $.ajax({
        type: "POST",
        url:url,
        data:data,
        success: function(r){
            console.log(r);
        },
        error: function(e){
            console.error(e.responseText);
        }
    });
}

function actualizar_pagos(){
    let data = {
        tipo:"actualizar_pagos"
    };

    $.ajax({
        type: "POST",
        url:url,
        data:data,
        success: function(r){
            console.log(r);
        },
        error: function(e){
            console.error(e.responseText);
        }
    });
}