let url = "../../api/control.php";
import{peso,ancho_barra} from './_apoyo.js';

Chart.register(ChartDataLabels);

$().ready(function(){
    total_ultimos_12();
    ventas_ultimos_12();
    ventas_ultimos_12_iva();
    promedio_ventas_ultimos_12();
    promedio_ventas_ultimos_12_iva();
    $("#nav_reportes").addClass("active");
});

function total_ultimos_12(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_total_ultimos_12"
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

        let ctx = $("#g_proyectos_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Total mes',
                        data: valores,                        
                        backgroundColor:"#4287f5",
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
                        ticks:{
                            display:false    
                        }                        
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

function ventas_ultimos_12(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_ventas_ultimos_12"
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

        let ctx = $("#g_ventas_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Total mes',
                        data: valores,
                        backgroundColor:"#4287f5",                        
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
                        ticks:{
                            display:false,                            
                        }                        
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

function ventas_ultimos_12_iva(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_ventas_ultimos_12_iva"
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
                        backgroundColor:"#4287f5",                        
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
                        ticks:{
                            display:false,                            
                        }                        
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


function promedio_ventas_ultimos_12(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_promedio_ventas_ultimos_12"
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

        let ctx = $("#g_promedio_ventas_ultimos_12")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        data: valores,
                        backgroundColor:"#4287f5",
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
                        ticks:{
                            display:false    
                        }                        
                    }
                },
                layout: {
                    padding: {
                        top: 10,
                    }
                }
            }
        });
    }
}

function promedio_ventas_ultimos_12_iva(){
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"reportes_promedio_ventas_ultimos_12_iva"
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

        let ctx = $("#g_promedio_ventas_ultimos_12_iva")[0].getContext('2d');
        let g_12 = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        data: valores,
                        backgroundColor:"#4287f5",
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
                        ticks:{
                            display:false    
                        }                        
                    }
                },
                layout: {
                    padding: {
                        top: 10,
                    }
                }
            }
        });
    }
}