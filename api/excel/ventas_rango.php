<?php
require '../../libs/vendor/autoload.php';
require 'apoyo.php';

$desde = filter_input(INPUT_GET,'desde',FILTER_UNSAFE_RAW);
$hasta = filter_input(INPUT_GET,'hasta',FILTER_UNSAFE_RAW);

$apoyo = new Apoyo();
$lista = $apoyo->ventas_rango($desde,$hasta);

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet(); 


$sheet->setCellValue('A1', 'FECHA');
$sheet->setCellValue('B1', '');
$sheet->setCellValue('C1', 'CLIENTE');
$sheet->setCellValue('D1', 'N1');
$sheet->setCellValue('E1', 'N2');
$sheet->setCellValue('F1', 'NOMBRE');
$sheet->setCellValue('G1', 'VALOR');

$trs = [];

for($i = 0; $i < count($lista); $i++){
    $anio = DateTime::createFromFormat("Y-m-d", $lista[$i]["fecha_inicio"]);
    
    $total = [
                $apoyo->formato_fecha($lista[$i]["fecha_inicio"]),
                "",
                $lista[$i]["cliente"],
                "CL" . $anio->format("y"),
                $lista[$i]["numero"],
                $lista[$i]["nombre"],
                $lista[$i]["valor"]                
    ];
    array_push($trs, $total);
}

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);

$spreadsheet->getActiveSheet()
->fromArray(
    $trs,   // The data to set
    NULL,   // Array values with this value will not be set
    'A2'    // Top left coordinate of the worksheet range where we want to set these values (default is A1)
);

// Write an .xlsx file  
$date = date('d-m-y-'.substr((string)microtime(), 1, 8));
$date = str_replace(".", "", $date);
$filename = "export_".$date.".xlsx";
$nombre = "Ventas_" . $desde . "a" . $hasta . ".xls";
$filePath = __DIR__ . DIRECTORY_SEPARATOR . $filename; //make sure you set the right permissions and change this to the path you want

try {
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
// $writer->save($filePath);
} catch(Exception $e) {
    exit($e->getMessage());
}

// redirect output to client browser
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$nombre.'"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
?>