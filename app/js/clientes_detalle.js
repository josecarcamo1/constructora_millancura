let url = "../../api/control.php";
import{rut} from './_apoyo.js';
let searchParams = new URLSearchParams(window.location.search);
let id_cliente = searchParams.get("id");

$().ready(function(){
    info();
    $("#rut").on("change",function(){
        $("#rut").val(rut($("#rut").val()));
    });

    $("#info_cliente").on("submit",function(e){
        e.preventDefault();
        editar();
    });

    //Comunas
    $('#com').on('change', function() {        
        let id_comuna = $("#comunas option[value='" + $('#com').val() + "']").attr('data-id_comuna');
        let id_provincia = $("#comunas option[value='" + $('#com').val() + "']").attr('data-id_provincia');

        if(id_provincia){
            console.log(id_provincia);
            prov_reg(id_provincia);
        }else{
            alert("Debe ingresar una comuna valida");
        }
    });

    lista_comunas();

    $('.btn_editar').on('click', function(){
        let form = $("#cargo");
        form.toggleClass('lectura editar');
        let isReadonly  = form.hasClass('lectura');
        form.find('input,textarea,select').prop('disabled', isReadonly);        
    });

    $('#borrar').on('click', function(e){        
        e.preventDefault();
        if(confirm("Se borrará el cliente y todos los proyectos asociados")){
            borrar(id_cliente);
        }
    }); 
});

function info(tipo){
    if(tipo === "Lectura"){        
        $("#info_cliente").addClass("lectura");
    }

    if(tipo === "Editar"){
        $("#info_cliente").addClass("editar");
        $form = $("#info_cliente");
        let items = $form.find('input,textarea,select');
        items.each(function(){
            if(!$(this).hasClass("fijo")){
                $(this).prop('disabled', false);
            }
        });
    }

    $.ajax({
        type: "POST",
        url: url,
        data:{
           tipo:"clientes_info",
           id_cliente:id_cliente
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

    function render(res){
        console.log(res);
        $("#nombre").val(res.nombre);
        $("#rut").val(res.rut);
        $("#telefono").val(res.telefono);
        $("#correo").val(res.mail);
        $("#direccion").val(res.direccion);
        $("#com").val(res.comuna);
        $("#provincia").val(res.provincia);
        $("#region").val(res.region);

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
            $("#spinner").hide();
        });        
    }
}

function editar(id){
    let comuna = ($("#comunas option[value='" + $('#com').val() + "']").attr('data-id_comuna') ? $("#comunas option[value='" + $('#com').val() + "']").attr('data-id_comuna') :"");
    let provincia = ($("#provincia").attr('data-id_provincia') ? $("#provincia").attr('data-id_provincia') :"");
    let region = ($("#region").attr('data-id_region') ? $("#region").attr('data-id_region') :"");

    let data = {
        tipo:"clientes_editar",
        nombre:$("#nombre").val(),
        rut:$("#rut").val(),
        direccion:$("#direccion").val(),
        telefono:$("#telefono").val(),
        correo:$("#correo").val(),
        comuna:comuna,
        provincia:provincia,
        region:region,
        id:id_cliente
    }

    console.log(data);

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        beforeSend: function(){
            $("#spinner").show();
            $(".emitir").attr("disabled", true);
        },
        success: function(res){
            alert(res);
            location.reload();
        },        
        error: function(e){
            alert(e);
            console.error(e.responseText);
        }
    });
}

function borrar(id){
    let data = {
        tipo:"clientes_borrar",
        id:id
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(res){
            alert(res);
            window.location.replace("clientes.php");
        },        
        error: function(e){
            alert(e);
            console.error(e.responseText);
        }
    });
}

function lista_comunas(){
    let data = {
        tipo:"lista_comunas"
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            // console.log(r);
            let res = JSON.parse(r);
            lista(res);
        },        
        error: function(e){
            console.error(e.responseText);
        }
    });
    
    function lista(com){
        console.log(com);
        for(let i = 0; i < com.length; i++){
            // console.log(com[i].nombre_may);
            let nombre_com = com[i].nombre_may;
            let id_provincia = com[i].id_provincia;
            let id_comuna = com[i].id;
            
            // trabajadores.push(r[i].rut + " | " + r[i].nombre);
            let item = "<option data-id_comuna=" + id_comuna + " data-id_provincia=" + id_provincia + " value='"+ nombre_com + "'>";
            $("#comunas").append(item);
        }
    }    
}

function prov_reg(id_provincia){
    let data = {
        tipo:"prov_reg",
        id_provincia:id_provincia
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            // console.log(r);
            let res = JSON.parse(r);
            // console.log(res);
            $("#provincia").val(res[0].nombre_may);
            $("#provincia").attr("data-id_provincia",res[0].id);
            $("#region").val(res[1].nombre_may);
            $("#region").attr("data-id_region",res[1].id);
        },        
        error: function(e){
            console.error(e.responseText);
        }
    });    
}
