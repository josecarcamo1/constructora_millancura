let url = "../../api/control.php";

$(document).ready(function(){
    $("#spinner").hide();

    $('#nuevo_usuario').submit(function(event){        
        event.preventDefault();
        agregar();
        $("input[type='submit']", this)
            .attr('disabled', 'disabled');
    });    
});

function agregar(){
    let nombre = $("#nombre").val();
    let correo = $("#correo").val();    
    let acceso = $("#selector_tipo_usuario").val();

    let data = {
        tipo:"usuarios_agregar",
        nombre:nombre,
        correo:correo,
        acceso:acceso
    }

    if(data.nombre == "" || data.correo == "" || data.permiso == ""){
        alert("Debe llenar todos los campos");
    }else{
        // console.log(data);
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
                window.location.replace("usuarios_lista.php");
            },
            error: function(e){            
                console.error(e.responseText);
            }
        });
    }
}