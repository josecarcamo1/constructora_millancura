let url = "../../api/control.php";
let searchParams = new URLSearchParams(window.location.search);
let id = searchParams.get("id");

let lista_vendedores_usuarios = [];

$(document).ready(function() {

    info();
    // permisos_por_id();
    // lista_contratos();
    // lista_accesos();
    // lista_permisos_contratos();

    $("#spinner").hide();

    $('#info_usuario').submit(function(event) {
        event.preventDefault();
        actualizar();
        $("input[type='submit']", this)
            .attr('disabled', 'disabled');
    });    

    $("#borrar_usuario").on("click", function(e) {
        e.preventDefault();
        if (confirm("Borrar usuario?")) {
            borrar_usuario();
        }
    });

    // $("#selector_tipo_usuario").on("change",function(e){
    //     if($("#selector_tipo_usuario").val() == "Vendedor"){
    //         lista_vendedores();
    //         $("#accesos_vendedores").show();
    //     }else{
    //         $("#accesos_vendedores").hide();
    //     }
    // });

    $("#agregar_acceso").on("click", function(e) {
        e.preventDefault();        
        agregar_acceso_vendedor();
    });
});

//////////////Usuario

function info() {
    $.ajax({
        type: "POST",
        url: url,
        data: {
            tipo: "usuarios_info",
            id: id
        },
        success: function(r) {            
            let res = JSON.parse(r);
            render(res);
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });

    function render(r) {
        console.log(r);
        $("#nombre").val(r[0].nombre);
        $("#correo").val(r[0].usuario);
        $("#selector_estado").val(r[0].estado);
        $("#selector_tipo_usuario").val(r[0].acceso);
        if(r[0].acceso == "Vendedor"){
            lista_vendedores();
            lista_vendedores_usuario();
            $("#accesos_vendedores").show();
        }
    }
}

function actualizar() {
    let nombre = $("#nombre").val();
    let estado = $("#selector_estado").val();
    let acceso = $("#selector_tipo_usuario").val();

    let data = {
        tipo: "usuarios_actualizar",
        id: id,
        nombre: nombre,
        estado: estado,
        acceso: acceso
    }

    console.log(data);

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(r) {
            console.log(r);
            alert(r);
            location.reload();
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}

function borrar_usuario() {
    let data = {
        tipo: "usuarios_borrar",
        id: id
    }

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(r) {
            alert(r);
            window.location.replace("usuarios_lista.php");
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}

function agregar_acceso_vendedor(){
    let id_vendedor = $("#selector_vendedores").val();

    let data = {
        tipo:"usuarios_agregar_vendedor",
        id_usuario:id,
        id_vendedor:id_vendedor
    }

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

function lista_vendedores(){    
    
    let data = {
        tipo:"vendedores_lista"
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            console.log(r);
            let res = JSON.parse(r);
            lista(res.data);
        },        
        error: function(e){
            console.error(e.responseText);
        }
    });
    
    function lista(r){
        console.log(r);
        for(let i = 0; i < r.length; i++){            
            let item = "<option value='"+ r[i].id + "'>" + r[i].nombre + "</option>";
            $("#selector_vendedores").append(item);
        }
    }
}

function borrar_acceso_vendedor(id_acceso){

    let data = {
        tipo:"usuarios_borrar_vendedor",
        id_acceso:id_acceso
    }

    if(confirm("Borrar vendedor asignado?")){
        $.ajax({
            type: "POST",
            url: url,
            data:data,
            success: function(r){
                console.log(r);
                alert(r);
                location.reload();
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    }    
}

function lista_vendedores_usuario(){
    $.ajax({
        type: "POST",
        url: url,
        data: {
            tipo: "usuarios_lista_vendedores",
            id_usuario: id
        },
        success: function(r) {
            console.log(r);
            let res = JSON.parse(r);
            render(res);
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });

    function render(r) {
        console.log(r);

        for (let i = 0; i < r.length; i++) {
            let num = i + 1;

            let item = '<tr id=' + r[i].id_uv + '>' +
                '<td class="text-left align-middle">' + num + '</td>' +
                '<td class="text-left align-middle">' + r[i].vendedor + '</td>' +
                '<td class="text-center align-middle"><button data-id="' + r[i].id_uv + '" type="button" class="btn btn-sm btn-danger borrar_acceso_contrato" title="Quitar Acceso"><i class="fa fa-times"></i></button></td>' +
                '</tr>';

            $("#lista_items").append(item);
            // lista_contratos_usuarios.push(r[i].id_contrato); //Para verificar si hay un vendedor duplicado
        }

        $(".borrar_acceso").on("click", function(e) {
            e.preventDefault();
            let id_acceso = $(this).data("id");
            borrar_acceso_vendedor(id_acceso);
        });
    }
}