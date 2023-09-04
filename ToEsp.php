<?php

include("conexion.php");
	$query ="SELECT * from envio_esp";					// selecciona todo del tabla estatus
	$result = mysqli_query($con,$query);
    $dataOut=(object)array();
    while($mostrar = mysqli_fetch_assoc($result)){
        $dataOut->id=$mostrar['id'];
        $dataOut->sensor=$mostrar['sensor'];
        $dataOut->valor=$mostrar['valor'];
        $myJSON=json_encode($dataOut);
        echo $myJSON;
    }
?>