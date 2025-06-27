let url = "../../api/control.php";
import{rut} from './_apoyo.js';
let searchParams = new URLSearchParams(window.location.search);
let id_vendedor = searchParams.get("id");

$().ready(function(){
    info();

    $("#info_vendedor").on("submit",function(e){
        e.preventDefault();
        editar();
    });
});

function info(tipo){
    $.ajax({
        type: "POST",
        url: url,
        data:{
           tipo:"vendedores_info",
           id_vendedor:id_vendedor
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
        $("#color").val(res.color);
        $("#selector_estados").val(res.estado);
    }
}

function editar(){

    let data = {
        id_vendedor:id_vendedor,
        tipo:"vendedores_editar",        
        nombre:$("#nombre").val(),
        color:$("#color").val(),
        estado:$("#selector_estados").val()
    }

    console.log(data);

    $.ajax({
        type: "POST",
        url: url,
        data:data,
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
