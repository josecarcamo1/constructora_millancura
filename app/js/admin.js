let searchParams = new URLSearchParams(window.location.search);
let tipo = searchParams.get("tipo");

$().ready(function(){
    selector(tipo);
});

function selector(tipo){
    if(tipo == "proyectos"){
        proyectos();
    }

    if(tipo == "clientes"){
        $("#titulo").append("Clientes");
    }
}

function proyectos(){

    $("#titulo").append("Proyectos");

    function crear(){
        let data = {
            nombre:$("#nombre").val,
            cliente:$("#cliente").val,
            tipo:$("#tipo").val,
        }
    }
    function borrar(){

    }
    function actualizar(){

    }
    function lista(){

    }
    function unico(){

    }
}