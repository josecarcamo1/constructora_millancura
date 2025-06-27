let url = "../../api/control.php";

$(document).ready(function(){
    $("#spinner").hide();
    leer_sesion();
});

function leer_sesion(){

    let data = {
        tipo:"leer_sesion"
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            let res = JSON.parse(r);
            console.log(res);
            $('#editar_contr').submit(function(event){        
                event.preventDefault();
                editar(res);
                $("input[type='submit']", this).attr('disabled', 'disabled');
            });
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function editar(res){
    console.log(res);
    let id_usuario = res["id"];
    let actual = $("#actual").val();    
    let nueva = $("#nueva").val();
    let usuario = res["usuario"];

    let data = {
        tipo:"usuarios_cambiar_contr",
        id:id_usuario,
        usuario:usuario,
        contr:actual,        
        contr_nueva:nueva
    }

    if(data.contr == "" || data.contr_nueva == ""){
        alert("Debe llenar todos los campos");
    }else if(data.contr_nueva.length < 8){
        alert("Debe tener al menos 8 caracteres");        
    }else{
        crear();
    }
    
    function crear(){
        console.log(data);
        $.ajax({
            type: "POST",
            url: url,
            data:data,
            success: function(r){
                console.log(r);
                alert(r);
                // location.reload();
                window.location.replace("../../api/salir.php");
            },
            
            error: function(e){            
                console.error(e.responseText);
            }
        });
    }
}