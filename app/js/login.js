let searchParams = new URLSearchParams(window.location.search);
let red = searchParams.get("red");
let url = "api/control.php";

$(document).ready(function() {
    check_sess();    
    $("#login").on("submit", function(e) {
        e.preventDefault();
        login();
    });
});

function check_sess() {
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"check_sess"
        },
        success: function(r) {
            if (r === "200") {                
                if(red){                    
                    window.location.replace(red);
                }else{
                    window.location.replace("app/vistas/operacion.php");
                }
            }
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}

function login() {
    let data = {
        usuario: $("#usuario").val(),
        contr: $("#contr").val()        
    }

    $.ajax({
        type: "POST",
        url: "api/aut.php",
        data: data,
        success: function(r) {      
            console.log(r);
            let res = JSON.parse(r);
            if(res.error){
                alert("Error: " + res.error);
            }else if(res.message === "OK") {
                crear_sesion();
            }
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}

function crear_sesion() {
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"crear_sesiones"
        },
        success: function(r) {
            console.log(r);
            if(r == "Ok"){
                window.location.replace("app/vistas/operacion.php");
            }            
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}