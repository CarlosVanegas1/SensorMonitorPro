#include <Arduino_JSON.h>
#include <SPI.h>
#include <Adafruit_GFX.h>
#include <Adafruit_PCD8544.h>

#include <WiFi.h>
#include <WiFiMulti.h>

#include <HTTPClient.h>

WiFiMulti wifiMulti;

const char* ssid1     = "SSID1";
const char* password1 = "PASSWORD1";

const char* ssid2     = "SSID2";
const char* password2 = "PASSWORD2";

const char* ssid3     = "SSID3";
const char* password3 = "PASSWORD3";

const char* ssid4     = "SSID4";
const char* password4 = "PASSWORD4";

const char* ssid5     = "SSID5";
const char* password5 = "PASSWORD5";


const char* serverHTTP = "http://casvaarj.000webhostapp.com/EspPost.php";   //SERVIDOR QUE INSERTA VALORES EN TABLA 'lectura_sensor'
const char* serverHTTP2 = "http://casvaarj.000webhostapp.com/ToEsp.php";    //SERVIDOR QUE REALIZA CONSULTA DE TABLA 'envio_esp'
int port = 80;
int var = 0;
char datos[40];
String answer = ""; //Variable para recibir respuesta desde HTTP POST
const char* ID="";
const char* VALOR="";
const char* SENSOR="";

const int pin_S1 = 34;
int valor_S1 = 0;
String nombre_S1 = "POT";

const int CLK = 18;
const int DIN = 23;
const int D_C = 04;
const int CE  = 15;
const int RST = 02;
Adafruit_PCD8544 display = Adafruit_PCD8544(CLK,DIN,D_C,CE,RST);

void setup()
{
  Serial.begin(115200);
  delay(10);
  wifiInit();
  nokiaInit();
}

void loop()
{
  valor_S1 = leer_sensor(pin_S1);
  Envio_POST(); //Envio de datos por metodo POST
  if (wifiMulti.run() == WL_CONNECTED){
    answer = Response(serverHTTP2);
    Serial.println("\n\nCONSULTA REALIZADA CORRECTAMENTE");
    Serial.println(answer);
    JSONVar myObject = JSON.parse(answer);
    ID=myObject["id"];
    VALOR=myObject["valor"];
    SENSOR=myObject["sensor"];
  }else{
    Serial.println("Error en la conexion WIFI");
  }
  nokiaPre();
}


int leer_sensor(int pin){              //REALIZAR LECTURA DEL SENSOR ANÁLOGO
  int lectura = analogRead(pin);
  int valor = map(lectura, 0, 4095, 0, 100);
  delay(200);
  return valor;
}

void nokiaPre(){                       //FUNCIÓN PARA MOSTRAR EN LA NOKIA 5110 EL DATO CONSULTADO
  display.clearDisplay();
  display.setTextColor(BLACK);
  display.setTextSize(1);
  display.setCursor(0,3);
  display.print("DATA CONSULTED");
  display.drawFastHLine(0,11,84,BLACK);
  display.drawFastHLine(0,13,84,BLACK);
  display.display();

  display.setCursor(1,17);
  display.print("ID:            ");
  display.setCursor(26,17);
  display.print(ID);
  display.setCursor(1,27);
  display.print("SEN:        ");
  display.setCursor(26,27);
  display.print(SENSOR);
  display.setCursor(1,37);
  display.print("VAL:        ");
  display.setCursor(26,37);
  display.print(VALOR);
  display.display();
}

void nokiaInit(){                     //FUNCIÓN QUE REALIZA LA INICIALIZACIÓN DE LA NOKIA 5110
  display.begin();
  display.setContrast(50);
  display.clearDisplay();

  display.setTextColor(WHITE, BLACK);
  display.setTextSize(1);
  display.setCursor(0,15);
  display.println("              ");
  display.setCursor(0,23);
  display.println("              ");
  display.setCursor(12,19);
  display.println("BIENVENIDO");
  display.display();
  delay(3000);
  display.clearDisplay();
  display.display();
  delay(200);
}
void wifiInit(void){                    //FUNCIÓN QUE REALIZA CONEXIÓN A CUALQUIERA DE LAS WIFI CONFIGURADAS
  wifiMulti.addAP(ssid1,password1);
  wifiMulti.addAP(ssid2,password2);
  wifiMulti.addAP(ssid3,password3);
  wifiMulti.addAP(ssid4,password4);
  wifiMulti.addAP(ssid5,password5);
  
  Serial.print("Conectándose a WiFi...");
  if(wifiMulti.run() == WL_CONNECTED) {
    Serial.println("");
    Serial.println("Conectado a WiFi");
    Serial.print("Dirección IP: ");
    Serial.println(WiFi.localIP());
  }
}

void Envio_POST(void){
  if (wifiMulti.run() == WL_CONNECTED){ 
     WiFiClient client;
     HTTPClient http;  // creo el objeto http
     http.begin(client,serverHTTP);
     http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Defino texto plano
     String datos_a_enviar = "Sensor=" + nombre_S1 + "&Valor=" + String(valor_S1); 
  
     int codigo_respuesta = http.POST(datos_a_enviar);
     Serial.print("Código HTTP: ");
     Serial.println(codigo_respuesta);
     if (codigo_respuesta>0){
        if (codigo_respuesta == 200){
          String cuerpo_respuesta = http.getString();
          Serial.println("El servidor respondió: ");
          Serial.println(cuerpo_respuesta);
        }
     } else {
        Serial.println("Error enviando POST \n");
     }
     http.end();  // libero recursos
  } else {
     Serial.println("Error en la conexion WIFI");
  }
  delay(10000); //espera 10s
}

String Response(const char* serverName){
  WiFiClient client;
  HTTPClient http;  // creo el objeto http
  http.begin(serverName);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  int httpResponseCode = http.GET();
  String payload = http.getString();
  http.end();  // libero recursos
  return payload;
}
