<?php
    $rutaArchivo = "PlantillaDocentes.csv";
    $url = "https://services.csmeducativo.com/webService/docente?_dc=1739226791370&formulario=docentes&tenant=%242y%2410%24wF385YWtFbgWAbJ.zmVGd.aR1bZGfg3APqggqr1rHGLySgjy3PfcC&idDocente=&idUsuario=7&codigoUsuario=csalazar&idEstudiante=&idTercero=7";

    if (($archivo = fopen($rutaArchivo, "r")) !== false) {
        fgetcsv($archivo, 1000, ";");
        $responseData = []; // Inicializar el array vacío
        while (($datos = fgetcsv($archivo, 1000, ";")) !== false) {
            
            $postData = [
                "foto" => "",
                "idTercero" => 0,
                "estado" => 1,
                "idTipoIdentificacion" => 1,
                "identificacion" => $datos[0],
                "nombres" => $datos[1],
                "apellidos" => $datos[2],
                "email" => $datos[3],
                "sexo" => ($datos[4] == "M") ? 1 : 0,
                "rh" => "",
                "telefono" => isset($datos[7]) ? $datos[7] : "",
                "direccion" => isset($datos[6]) ? $datos[6] : "",
                "fechaNacimiento" => isset($datos[5]) ? $datos[5] : "",
                "idDocenteReemplazar" => 1,
                "asignacionAcademica" => [],
                "direccionGrupo" => [],
                "idCiudadDocumento" => 0,
                "acronimoIdentificacino" => "",
                "identificacionCompuesta" => "",
                "nombreCompleto" => "",
                "usuarioCreaTercero" => "",
                "fechaCreacionTercero" => "",
                "usuarioCreacionDocente" => "",
                "fechaCreacionDocente" => ""
            ];

            $postDataJson = json_encode($postData);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decodeResponse = json_decode($response);

            $nuevoRegistro = [
                "estado" => $httpCode,
                "mensaje" => $decodeResponse->mensaje,
                "docente" => $datos[0]." - ".$datos[1]." ".$datos[2]
            ];
            // Agregar al arreglo sin sobrescribir
            $responseData[] = $nuevoRegistro;
        }
        echo json_encode($responseData);

        fclose($archivo);

    } else {
        echo "No se pudo abrir el archivo";
    }
?>
