let url = "../../api/control.php";

$(document).ready(function(){    
    leer_sesion();
});

function leer_sesion(){
    $.ajax({
        type: "POST",
        url: url,
        data: {
            tipo: "leer_sesion",
        },
        success: function (r) {
            let res = JSON.parse(r);
            nav(res);
        },
        error: function (e) {
            console.error(e.responseText);
        },
    });

    function nav(sess){
        if(sess.acceso == "Administrador"){
            $("#admin").append('<a class="btn text-white" href="usuarios_lista.php" title="Administración"><i class="fas fa-cog"></i></a>');
        }
    }
}