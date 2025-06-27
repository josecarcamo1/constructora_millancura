let url = "../../api/control.php";
import{rut} from './_apoyo.js';

$().ready(function(){    
    $("#rut").on("change",function(){
        $("#rut").val(rut($("#rut").val()));
    });

    $("#nuevo_cliente").on("submit",function(e){
        e.preventDefault();
        crear_cliente();
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

    $("#spinner").hide();
    lista_comunas();
});

function crear_cliente(){
    let comuna = ($("#comunas option[value='" + $('#com').val() + "']").attr('data-id_comuna') ? $("#comunas option[value='" + $('#com').val() + "']").attr('data-id_comuna') :"");
    let provincia = ($("#provincia").attr('data-id_provincia') ? $("#provincia").attr('data-id_provincia') :"");
    let region = ($("#region").attr('data-id_region') ? $("#region").attr('data-id_region') :"");

    let data = {
        tipo:"clientes_agregar",
        nombre:$('#nombre').val(),
        rut:$('#rut').val(),
        telefono:$('#telefono').val(),
        correo:$('#correo').val(),
        direccion:$('#direccion').val(),
        comuna:comuna,
        provincia:provincia,
        region:region
    }

    // console.log(data);

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        beforeSend: function(){
            $("#spinner").show();
            $(".emitir").attr("disabled", true);
        },
        success: function(res){
            // console.log(res);
            // $("#spinner").hide();
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
