let url = "../../api/control.php";
import{peso,ancho_barra} from './_apoyo.js';

Chart.register(ChartDataLabels);

$().ready(function(){
    leer_sesion();
    $("#nav_reportes").addClass("active");

    //Cambio vendedores
    $('#selector_vendedores').on('change', function() {
        cambiar_sesion_vendedor(this.value);
    });
});

function leer_sesion(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"leer_sesion"
        },
        success: function(r){
            ejecutar(JSON.parse(r));            
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function cambiar_sesion_vendedor(id_vendedor) {
    console.log(id_vendedor);
    $.ajax({
        type: "POST",
        url: url,
        data: {
            tipo: "cambiar_sesion",
            tipo_sesion: "Vendedor",
            nuevo: id_vendedor
        },
        success: function(r) {
            console.log(r);
            location.reload();
        },
        error: function(e) {
            console.error(e.responseText);
        }
    });
}

function ejecutar(sess){
    console.log(sess);

    let id_vendedor = sess.id_vendedor;
    
    lista_vendedores(sess);
    info_vendedor(id_vendedor);
}

function info_vendedor(id_vendedor){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"vendedores_info",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let info = JSON.parse(r);
            
            total_ultimos_12(id_vendedor,info);
            ventas_ultimos_12(id_vendedor,info);
            ventas_ultimos_12_iva(id_vendedor,info);
            promedio_ventas_ultimos_12(id_vendedor,info);
            promedio_ventas_ultimos_12_iva(id_vendedor,info);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });
}

function total_ultimos_12(id_vendedor,info){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_total_ultimos_12_vendedores",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let res = JSON.parse(r);
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        let nombres = [];
        let valores = [];

        for(let i = r.length - 1; i >= 0; i--){
            nombres.push(r[i][0] + "/" + r[i][1]);
            valores.push(r[i][2]);
        }

        let ctx = $("#g_proyectos_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Total mes',
                        data: valores,                        
                        backgroundColor:info.color,
                        datalabels: {                            
                            color: '#101010',
                            anchor:"end",
                            align:"top"
                        }
                    }
                ]
            },
            options: {
                barThickness: ancho_barra,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display:false                        
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                    }
                }
            }
        });
    }
}

function ventas_ultimos_12(id_vendedor,info){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_ventas_ultimos_12_vendedores",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let res = JSON.parse(r);            
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        let nombres = [];
        let valores = [];

        for(let i = r.length - 1; i >= 0; i--){
            nombres.push(r[i][0] + "/" + r[i][1]);
            valores.push(r[i][2]);
        }

        let ctx = $("#g_ventas_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Total mes',
                        data: valores,
                        backgroundColor:info.color,
                        datalabels: {                            
                            color: '#101010',
                            anchor:"end",
                            align:"top",
                            display: function(context) {
                                return isNaN(context.dataset.data[context.dataIndex])?"":context.dataset.data[context.dataIndex];
                            },
                            formatter: function (value) {
                                return  peso(parseInt(value));
                            }
                        }
                    }
                ]
            },
            options: {
                barThickness: ancho_barra,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display:false                        
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                    }
                }
            }
        });
    }
}

function ventas_ultimos_12_iva(id_vendedor,info){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_ventas_ultimos_12_vendedores_iva",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let res = JSON.parse(r);            
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        console.log(r);

        let nombres = [];
        let valores = [];

        for(let i = r.length - 1; i >= 0; i--){
            nombres.push(r[i][0] + "/" + r[i][1]);
            valores.push(r[i][2]);
        }

        let ctx = $("#g_ventas_ultimos_12_iva")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Total mes',
                        data: valores,
                        backgroundColor:info.color,
                        datalabels: {                            
                            color: '#101010',
                            anchor:"end",
                            align:"top",
                            display: function(context) {
                                return isNaN(context.dataset.data[context.dataIndex])?"":context.dataset.data[context.dataIndex];
                            },
                            formatter: function (value) {
                                return  peso(parseInt(value));
                            }
                        }
                    }
                ]
            },
            options: {
                barThickness: ancho_barra,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display:false                        
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                    }
                }
            }
        });
    }
}

function promedio_ventas_ultimos_12(id_vendedor,info){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_promedio_ventas_ultimos_12_vendedores",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let res = JSON.parse(r);            
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        let nombres = [];
        let valores = [];

        for(let i = r.length - 1; i >= 0; i--){
            nombres.push(r[i][0] + "/" + r[i][1]);
            valores.push(r[i][2]);
        }

        let ctx = $("#g_promedio_ventas_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        data: valores,
                        backgroundColor:info.color,
                        datalabels: {                            
                            color: '#101010',
                            anchor:"end",
                            align:"top",
                            display: function(context) {
                                return isNaN(context.dataset.data[context.dataIndex])?"":context.dataset.data[context.dataIndex];
                            },
                            formatter: function (value) {
                                return  peso(parseInt(value));
                            }
                        }
                    }
                ]
            },
            options: {
                barThickness: ancho_barra,
                responsive:true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display:false                        
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                    }
                }
            }
        });
    }
}

function promedio_ventas_ultimos_12_iva(id_vendedor,info){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_promedio_ventas_ultimos_12_vendedores_iva",
            id_vendedor:id_vendedor
        },
        success: function(r){
            let res = JSON.parse(r);            
            render(res);
        },
        error: function(e){            
            console.error(e.responseText);
        }
    });

    function render(r){
        let nombres = [];
        let valores = [];

        for(let i = r.length - 1; i >= 0; i--){
            nombres.push(r[i][0] + "/" + r[i][1]);
            valores.push(r[i][2]);
        }

        let ctx = $("#g_promedio_ventas_ultimos_12_iva")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        data: valores,
                        backgroundColor:info.color,
                        datalabels: {                            
                            color: '#101010',
                            anchor:"end",
                            align:"top",
                            display: function(context) {
                                return isNaN(context.dataset.data[context.dataIndex])?"":context.dataset.data[context.dataIndex];
                            },
                            formatter: function (value) {
                                return  peso(parseInt(value));
                            }
                        }
                    }
                ]
            },
            options: {
                barThickness: ancho_barra,
                responsive:true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display:false                        
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                    }
                }
            }
        });
    }
}

function lista_vendedores(sess) {
    selector(sess.vendedores);
    function selector(r) {
        for (let i = 0; i < r.length; i++) {
            let item = "<option value='" + r[i].id + "'>" + r[i].nombre + "</option>";
            $("#selector_vendedores").append(item);
        }
    }
    $("#selector_vendedores option[value='" + sess.id_vendedor + "']").attr("selected", "selected");
}