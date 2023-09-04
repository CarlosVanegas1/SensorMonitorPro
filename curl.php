<?php
$url = "https://esp32-nosql-default-rtdb.firebaseio.com/UsersData.json";

$ch = curl_init(); // inicio curl
curl_setopt($ch, CURLOPT_URL, $url);//se asigna la URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //habilito la recepción de resultados
$response=curl_exec($ch); //ejecuta una consulta general de la data base
curl_close($ch); 
print_r($response);
$data1=json_decode($response,true);
foreach($data1 as $key =>$value)
{
    $hume=$data1[$key]["humidity"];
    $pres=$data1[$key]["pressure"];
    $temp=$data1[$key]["temperature"];
}
//echo("<br>Humedad: ".$hume."<br>Presion: ".$pres."<br>Temperatura: " .$temp); 
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ESP IoT Firebase App</title>

    <!-- update the version number as needed -->
    <script src="https://www.gstatic.com/firebasejs/8.8.1/firebase-app.js"></script>

    <!-- include only the Firebase features as you need -->
    <script src="https://www.gstatic.com/firebasejs/8.8.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.8.1/firebase-database.js"></script>

    <script>
     // Firebase configuration
     const firebaseConfig = {
      apiKey: "AIzaSyDQUA7sPUFlhbFQegdGCTN_9Du1tDQx76Q",
      authDomain: "esp32-nosql.firebaseapp.com",
      projectId: "esp32-nosql",
      storageBucket: "esp32-nosql.appspot.com",
      messagingSenderId: "244230807519",
      appId: "1:244230807519:web:b9c08402cdc830b729ee6a",
      measurementId: "G-1R6S1G31EQ"
      };

      // Initialize firebase
      firebase.initializeApp(firebaseConfig);

      // Make auth and database references
      const auth = firebase.auth();
      const db = firebase.database();

    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!--<link rel="icon" type="image/png" href="favicon.png">-->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <!--TOP BAR-->
  <div class="topnav">
    <h1>LECTURA NoSQL <i class="fas fa-clipboard-list"></i></h1>
  </div>

  <!--CONTENT (SENSOR READINGS)-->
  <div class="content-sign-in" id="content-sign-in" style="display: none;">
    <div class="cards">
      <!--TEMPERATURE-->
      <div class="card">
        <p><i class="fas fa-thermometer-half" style="color:#e72b3b;"></i> TEMPERATURA</p>
        <p><span class="reading"><span id="temp"></span> &deg;C</span></p>
      </div>
      <!--HUMIDITY-->
      <div class="card">
        <p><i class="fas fa-tint" style="color:#00add6;"></i> HUMEDAD</p>
        <p><span class="reading"><span id="hum"></span> &percnt;</span></p>
      </div>
      <!--PRESSURE-->
      <div class="card">
        <p><i class="fas fa-rocket" style="color:#e47637;"></i> PRESIÓN</p>
        <p><span class="reading"><span id="pres"></span> hPa</span></p>
      </div>
    </div>
  </div>
    <script src="scripts/auth.js"></script>
    <script src="scripts/index.js"></script>
  </body>
</html>