<?php
include'conexion.php';
if ($con) {
    echo "Conexion con base de datos exitosa! \n";
    
    if(isset($_POST['Sensor'])) {
        $Sensor = $_POST['Sensor'];
        echo "DATOS ENVIADOS A LA BD \n";
        echo "  Nombre Sensor : ".$Sensor."\n";
    }
 
    if(isset($_POST['Valor'])) { 
        $Valor = $_POST['Valor'];
        echo "  Valor : ".$Valor;
        
        date_default_timezone_set('america/bogota');
        $fecha_actual = date("Y-m-d H:i:s");
        
        $consulta = "INSERT INTO lectura_sensor(id,nombre_sensor, lectura, hora_lectura) 
        VALUES (NULL,'$Sensor',$Valor,'$fecha_actual')";
        $resultado = mysqli_query($con, $consulta);
        if ($resultado){
            echo "\nRegisto en base de datos: OK!\n";
        } else {
            echo "\nRegisto en base de datos: ERROR!\n";
        }
    }
    
    
} else {
    echo "\n Falla! conexion con Base de datos ";   
}


?>