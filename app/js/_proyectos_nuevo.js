import{rnd,peso} from './_apoyo.js';
let url = "../../api/control.php";
let multiple = false;

$().ready(function(){
    $("#cantidad").on("change",function(){
        let cantidad = $("#cantidad").val();
        if(cantidad == 1){
            multiple = false;
            console.log("Simple");
        }else{
            multiple = true;
            console.log("Multiple");
        }
    });

    $("#valor").on("change",function(){
        let valor = $("#valor").val();
        $("#valor_iva").val(rnd(valor*1.019));
    });

    $("#nuevo_proyecto").on("submit",function(e){
        e.preventDefault();
        crear_proyecto();        
    });

    fecha_creacion.max = moment();
    document.getElementById('fecha_creacion').valueAsDate = new Date();

    $("#spinner").hide();
    lista_clientes();
    lista_vendedores();
});

function crear_proyecto(){
    let cantidad = parseInt($("#cantidad").val());
    let nombre = $("#nombre").val();
    let cliente = $("#clientes option[value='" + $("#tr").val() + "']").attr("data-id");
    let vendedor = $("#lista_vendedores").val();
    let estado_iva = parseInt($("#estado_iva").val());
    let fecha = $("#fecha_creacion").val();
    let valor = parseInt($("#valor").val());    

    let data = {
        tipo:"proyecto_agregar",
        cantidad:cantidad,
        cliente:cliente,
        vendedor:vendedor,
        estado_iva:estado_iva,
        nombre:nombre,
        fecha_inicio:fecha,
        valor:valor
    }
    
    if(isNaN(parseInt(cliente))){        
        data.cliente = $("#tr").val();
    }

    console.log(data);

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            alert(r);
            window.location.replace("operacion.php");
        },        
        error: function(e){
            console.error(e.responseText);
        }
    });
}

function lista_clientes(){
    let data = {
        tipo:"clientes_lista"
    }

    $.ajax({
        type: "POST",
        url: url,
        data:data,
        success: function(r){
            let res = JSON.parse(r);
            selector(res);
        },        
        error: function(e){
            console.error(e.responseText);
        }
    });

    function selector(r) {
        let clientes = [];
        for (let i = 0; i < r.length; i++) {
            // clientes.push(r[i].rut + " | " + r[i].nombre);
            let item = "<option data-id=" + r[i].id + " value='" + r[i].nombre + "'>";
            $("#clientes").append(item);
        }

        var substringMatcher = function (strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, "i");

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function (i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };

        $("#selector_clientes .typeahead").typeahead(
            {
                hint: true,
                highlight: true,
                minLength: 1,
            },
            {
                name: "clientes",
                source: substringMatcher(clientes),
            }
        );
    }
}

function lista_vendedores(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"vendedores_lista_selector"
        },
        success: function(res){
            let r = JSON.parse(res);
            render(r);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        console.log(r);
        for(let i = 0; i < r.length; i++){
            if(r[i].estado != "0"){
                let item = '<option value="' + r[i].id + '">' + r[i].nombre + '</option';
                $("#lista_vendedores").append(item);
            }
        }
    }
}

