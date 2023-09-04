<?php
    include'conexion.php'; 
    function mostrar($consulta){//FUNCIÓN PARA REALIZAR UNA CONSULTA Y MOSTRAR EL RESULTADO DE ESTA
        /* APARTADO NoSQL*/
        $url = "https://esp32-nosql-default-rtdb.firebaseio.com/UsersData.json";
        $ch = curl_init(); // inicio curl
        curl_setopt($ch, CURLOPT_URL, $url);//se asigna la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //habilito la recepción de resultados
        $response=curl_exec($ch); //ejecuta una consulta general de la data base
        curl_close($ch); 
        $data1=json_decode($response,true);
        foreach($data1 as $key =>$value)
        {
            $hume=$data1[$key]["humidity"];
            $pres=$data1[$key]["pressure"];
            $temp=$data1[$key]["temperature"];
        }
        while($mostrar = mysqli_fetch_assoc($consulta)){
            ?>
            <tr>
                <td><?php echo $mostrar['id'] ?></td>
                <td><?php echo $mostrar['nombre_sensor'] ?></td>
                <td><?php echo $mostrar['lectura'] ?></td>
                <td><?php echo $mostrar['hora_lectura'] ?></td>
                <td><table>
                    <tr>
                        <td><?php echo("H:".$hume); ?></td>
                        <td><?php echo("P:".$pres); ?></td>
                        <td><?php echo("T:".$temp); ?></td>
                    </tr>
                </table></td>
            </tr>
            <?php
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf'8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> BASE DE DATOS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="formato.css">
    </head>
    <body>
        <div id="encabezado">
            <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
                <table id="title">
                    <tr>
                        <th id="title" colspan='4'><h1>BASE DE DATOS MySQL: LEER SENSOR</h1></th>
                    </tr>
                    <tr>
                        <th id="title">
                            <label>ID:</label>
                            <input type="text" name="id" style="width: 40px">
                        </th>
                        <th id="title">
                            <input type="submit" name ="enviar" value="BUSCAR Y ENVIAR" class="btn btn-bootstrap" id="bEnviar">                   
                        </th>
                        <th id="title">
                            <a href="consulta_ind.php" class="btn btn-enlace" id="bMostrar">MOSTRAR TABLA COMPLETA</a>
                        </th>
                        <th id="title">
                        <input type="submit" name ="reset" value="REINICIAR" class="btn btn-bootstrap" id="bReset">
                        </th> 
                    </tr>
                </table>
            </form>
        </div>
        <div id="main-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SENSOR</th>
                        <th>VALOR</th>
                        <th>FECHA Y HORA</th>
                        <th>No SQL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(isset($_POST['enviar'])){
                            //MOSTRAR LA BUSQUEDA
                            $id = $_POST['id'];
                            if(empty($_POST['id'])){
                                echo "<script language= 'JavaScript'>
                                    alert('Ingresa el ID que deseas buscar');
                                    location.assign('consulta_ind.php')
                                    </script>";
                            }else{
                                $dataOut=(object)array();
                                $sql1= "SELECT * FROM lectura_sensor where id=".$id;
                            }
                            $consID = mysqli_query($con,$sql1);
                            if(!($consID->num_rows > 0)){
                                echo "<script language= 'JavaScript'>
                                    alert('Este ID no existe, intente nuevamente con otro valor.');
                                    location.assign('consulta_ind.php')
                                    </script>";
                            }else{
                                mostrar($consID);
                                foreach ($con->query($sql1) as $row) {
                                    $dataOut->id=$row['id'];
                                    $dataOut->sensor=$row['nombre_sensor'];
                                    $dataOut->valor=$row['lectura'];
                                    $myJSON=json_encode($dataOut);
                                }
                                $obj = json_decode($myJSON);
                                $sql = "UPDATE envio_esp SET id='$obj->id', sensor='$obj->sensor', valor='$obj->valor'";
                                if ($con->query($sql) === TRUE) {} 
                            }
                        }elseif(isset($_POST['reset'])){
                            //REINICAR EL MUESTREO DE LA TABLA
                            $sql2="DELETE FROM lectura_sensor";
                            $sql3="ALTER TABLE lectura_sensor AUTO_INCREMENT=1";
                            $del_tab    = mysqli_query($con,$sql2); #Borrar datos de la tabla
                            $res_AI     = mysqli_query($con,$sql3); #Reiniciar el valor del Auto Increment ID = 1
                            $sql = "SELECT * from lectura_sensor";
                            $act_tabla  = mysqli_query($con,$sql);  #Consultar la tabla actualizada
                            mostrar($act_tabla);               #Mostrar la tabla actualizada
                        }else{
                            $sql = "SELECT * from lectura_sensor";
                            $cons_tabla = mysqli_query($con,$sql); #Consultar la tabla actualizada
                            mostrar($cons_tabla);              #Mostrar la tabla actualizada 
                        }
                        /* APARTADO NoSQL
                        $url = "https://esp32-nosql-default-rtdb.firebaseio.com";
                        $ch = curl_init(); // inicio curl
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //Desativo el certificado de seguridad
                        curl_setopt($ch, CURLOPT_URL, $url);//se asigna la URL
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //habilito la recepción de resultados
                        $response=curl_exec($ch); //ejecuta una consulta general de la data base
                        curl_close($ch); 
                        //print_r($response);
                        $data1=json_decode($response,true);

                        /*foreach($data1 as $key =>$value)
                        {  $x1=$data1[$key]["humidity"];
                            $x2=$data1[$key]["pressure"];
                            $x3=$data1[$key]["temperature"];

                        }  
                        echo($data1->humidity. "  ".$data1->pressure. "  " .$data1->temperature);*/
                        ?>
                </tbody>
            </table>
        </div>
    </body>
</html>