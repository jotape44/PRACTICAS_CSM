<?php
$linea = 0;
$archivo = fopen("docentejer.csv", "r");
while (($datos = fgetcsv($archivo)) !== true) {
    $num = count($datos);
    for ($columna = 0; $columna < $num; $columna++) {
        echo $datos[$columna] . "\n";
    }
}
fclose($archivo);
?>