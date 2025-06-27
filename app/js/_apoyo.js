export const ancho_barra = 20;

export function rnd(n){
    return Math.round(n * 10)/10;
}

export function ceil(n){
    return Math.ceil(n);
}

export function rut(r){
    let rut = r.replace(/[^a-z0-9]/gi,'');
    rut = rut.slice(0, -1) + "-" + rut.slice(-1);
    return rut;
}

export function rut_existente(rut,callback) {
    $.ajax({
        type: "POST",
        url: url,
        data:{
            tipo:"existe_rut",
            rut:rut
        },
        success: function(r){
            console.log(r);
            if(r === 0){
                callback();
            }else{
                alert("Rut ya existente en base de datos");
            }            
        },        
        error: function(e){
            console.error(e.responseText);            
        }
    });
}

export function rut_valido(rut) {
    if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test(rut)) return false;
    var tmp = rut.split("-");
    var digv = tmp[1];
    var rut = tmp[0];
    if (digv == "K") digv = "k";
    return dv(rut) == digv;
    function dv(T) {
        var M = 0,
            S = 1;
        for (; T; T = Math.floor(T / 10))
            S = (S + (T % 10) * (9 - (M++ % 6))) % 11;
        return S ? S - 1 : "k";
    }
}

export function calcular_dias(fecha_creacion){
    return moment().diff(fecha_creacion,"days");
}

export function peso(valor) {
    return "$ " + valor.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ".");
}

export function format_fecha(fecha){
    return moment(fecha).format("DD/MM/YYYY");
}

export function nombre_mes(n){
    let mes = "";
    switch(parseInt(n)) {
        case "01":
            mes = "ENE";
            break;
        case "02":
            mes = "FEB";
            break;
        case "03":
            mes = "MAR";
            break;
        case "04":
            mes = "ABR";
            break;
        case "05":
            mes = "MAY";
            break;
        case "06":
            mes = "JUN";
            break;
        case "07":
            mes = "JUL";
            break;
        case "08":
            mes = "AGO";
            break;
        case "09":
            mes = "SEP";
            break;
        case "10":
            mes = "OCT";
            break;
        case "11":
            mes = "NOV";
            break;
        case "12":
            mes = "DIC";
            break;
        default:
            mes = "-";
            break;
    }
    return mes;
}

