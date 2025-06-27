<?php
function procesarArchivosCamara(PDO $pdo) {
    // 1. Configuración de rutas
    $basePath = realpath(__DIR__ . '/../../../prueba') . '/';
    $imagenesPath = realpath(__DIR__ . '/../../../imagenes') . '/';

    if (!is_dir($basePath)) {
        die("Error: La carpeta 'prueba' no existe.");
    }
    if (!is_dir($imagenesPath)) {
        die("Error: La carpeta 'imagenes' no existe.");
    }

    // 2. Obtener archivos JS
    $archivos = scandir($basePath);

    // Filtrar archivos .js
    $archivosJS = array_filter($archivos, function($archivo) {
        return pathinfo($archivo, PATHINFO_EXTENSION) === 'js';
    });

    if (empty($archivosJS)) {
        return [
            'archivos_procesados' => 0,
            'registros_insertados' => 0,
            'patentes_creadas' => 0
        ];
    }

    $insertados = 0;
    $creadas = 0;

    foreach ($archivosJS as $archivo) {
        $rutaCompleta = $basePath . $archivo;
        $contenido = file_get_contents($rutaCompleta);

        if ($contenido === false) {
            continue;
        }

        $data = json_decode($contenido, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            continue;
        }

        $plate = $data['infoplate']['Plate'] ?? null;
        $fecha = $data['infoplate']['DateHour'] ?? null;

        if (!$plate || !$fecha) {
            continue;
        }

        // BUSCAR IMÁGENES RELACIONADAS CON _5_
        $nombreBaseOriginal = pathinfo($archivo, PATHINFO_FILENAME);
        
        // Reemplazar _JSON_ por _IMAGE_ y asegurar _5_
        $nombreBaseImagen = str_replace('_JSON_', '_IMAGE_', $nombreBaseOriginal);
        $nombreBaseImagen = preg_replace('/_\d+_FTP_ACTION_IMAGE_/', '_5_FTP_ACTION_IMAGE_', $nombreBaseImagen);
        
        // Patrones exactos para las imágenes
        $fotoGeneralPath = $imagenesPath . $nombreBaseImagen . '.jpg';
        $fotoPatentePath = $imagenesPath . $nombreBaseImagen . '_Cut.jpg';
        
        // Verificar existencia de archivos
        $fotoGeneral = file_exists($fotoGeneralPath) ? basename($fotoGeneralPath) : null;
        $fotoPatente = file_exists($fotoPatentePath) ? basename($fotoPatentePath) : null;

        // Convertir fecha a formato DateTime para manipulación
        try {
            $fechaObj = new DateTime($fecha);
            $fechaFormateada = $fechaObj->format('Y-m-d H:i:s');
            $fechaInicioRango = (clone $fechaObj)->modify('-20 minutes')->format('Y-m-d H:i:s');
            $fechaFinRango = (clone $fechaObj)->modify('+20 minutes')->format('Y-m-d H:i:s');
            $fechaInicioDia = $fechaObj->format('Y-m-d 00:00:00');
            $fechaFinDia = $fechaObj->format('Y-m-d 23:59:59');
        } catch (Exception $e) {
            continue;
        }

        // Buscar la patente en la tabla patentes
        $stmt = $pdo->prepare("SELECT pat_id, pat_emp_id FROM patentes WHERE pat_patente = ?");
        $stmt->execute([$plate]);
        $patente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patente) {
            // Si no existe, crearla con empresa 12
            $insertPat = $pdo->prepare("INSERT INTO patentes (pat_patente, pat_emp_id) VALUES (?, ?)");
            if ($insertPat->execute([$plate, 12])) {
                $pat_id = $pdo->lastInsertId();
                $emp_id = 12;
                $creadas++;
            } else {
                continue;
            }
            
            $tipo = 'entrada';
        } else {
            $pat_id = $patente['pat_id'];
            $emp_id = $patente['pat_emp_id'];
            
            // Verificar si hay registros recientes (en el rango de ±5 minutos)
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM camara 
                                  WHERE cam_pat_id = ? 
                                  AND cam_fecha_hora BETWEEN ? AND ?");
            $stmt->execute([$pat_id, $fechaInicioRango, $fechaFinRango]);
            $registrosRecientes = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si hay registros en el rango de 5 minutos, saltar este archivo
            if ($registrosRecientes['total'] > 0) {
                @unlink($rutaCompleta); // Eliminar archivo procesado igualmente
                continue;
            }
            
            // Verificar registros del mismo día para determinar el tipo
            $stmt = $pdo->prepare("SELECT cam_id, cam_tipo FROM camara 
                                   WHERE cam_pat_id = ? 
                                   AND cam_fecha_hora BETWEEN ? AND ?
                                   ORDER BY cam_fecha_hora DESC
                                   LIMIT 1");
            $stmt->execute([$pat_id, $fechaInicioDia, $fechaFinDia]);
            $ultimoRegistro = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Determinar tipo basado en el último registro del día
            if ($ultimoRegistro) {
                $tipo = ($ultimoRegistro['cam_tipo'] === 'entrada') ? 'salida' : 'entrada';
            } else {
                $tipo = 'entrada';
            }
        }

        // Insertar en la tabla camara
        $insert = $pdo->prepare("INSERT INTO camara 
                                (cam_pat_id, cam_emp_id, cam_fecha_hora, cam_tipo, cam_foto_general, cam_foto_patente, cam_json) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($insert->execute([$pat_id, $emp_id, $fechaFormateada, $tipo, $fotoGeneral, $fotoPatente, json_encode($data)])) {
            $insertados++;
            
            // Eliminar archivos procesados
            @unlink($rutaCompleta);
            // if ($fotoGeneral) @unlink($fotoGeneralPath);
            // if ($fotoPatente) @unlink($fotoPatentePath);
        }
    }

    return [
        'archivos_procesados' => count($archivosJS),
        'registros_insertados' => $insertados,
        'patentes_creadas' => $creadas
    ];
}
?>