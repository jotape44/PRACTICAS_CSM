<?php
    $rutaArchivo = "docentejer.csv";

    if (($archivo = fopen($rutaArchivo, "r")) !== false) {

        echo "<table border='1'>"; 
        while (($datos = fgetcsv($archivo, 1000, ";")) !== false) {
            
            echo "<tr>";
            echo "<td>".$datos[0]."</td>";
            echo "<td>".$datos[1]."</td>";
            echo "<td>".$datos[2]."</td>";
            echo "<td>".$datos[3]."</td>";
            echo "<td>".$datos[4]."</td>";
            echo "<td>".$datos[5]."</td>";
            echo "<td>".$datos[6]."</td>";
            echo "</tr>";
            
        }

        echo "</table>";

        fclose($archivo); 

    } else {
        echo "No se pudo abrir el archivo";
    }
?>
