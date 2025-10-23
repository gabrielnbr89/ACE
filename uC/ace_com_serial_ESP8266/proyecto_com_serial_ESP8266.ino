#include <ESP8266WiFi.h>
#include <PubSubClient.h>

//-------------------VARIABLES GLOBALES--------------------------
//char topic[50];
int contconexion = 0;
const char* ssid = "RouterPF";
const char* password = "proyecto";
char   SERVER[20]   = "192.168.2.1";
int    SERVERPORT   = 1883;
const char* USERNAME = "ace1";   
const char* PASSWORD = "";
char TOPICO[25];

unsigned long previousMillis = 0;

char PLACA[10];
char CLAVE[10];
char cadena[10];
String topicoFrecuencia="/aces/ace1/frecuencia";
String topicoTension="/aces/ace1/tension";
String topicoIntensidad="/aces/ace1/intensidad";
String topicoPotencia="/aces/ace1/potencia";
String topicoFp="/aces/ace1/fp";
String frecuencia,tension,intensidad,potencia,fp;

//-------------------------------------------------------------------------
WiFiClient espClient;
PubSubClient client(espClient);

//------------------------CALLBACK-----------------------------
void callback(char* topic, byte* payload, unsigned int length) {
  
}

//------------------------RECONNECT-----------------------------
void reconnect() {
  uint8_t retries = 3;
  // Loop hasta que estamos conectados
  while (!client.connected()) {
//     Crea un ID de cliente al azar
    String clientId = "ESP8266Client-";
    clientId += String(random(0xffff), HEX);
//     Attempt to connect
//    USERNAME.toCharArray(PLACA, 10);
//    PASSWORD.toCharArray(CLAVE, 10);
    if (client.connect("", USERNAME, PASSWORD)) {
    } else {
      // espera 5 segundos antes de reintentar
      delay(5000);
    }
    retries--;
    if (retries == 0) {
      // esperar a que el WDT lo reinicie
      while (1);
    }
  }
}

//------------------------SETUP-----------------------------
void setup() {
  // Inicia Serial
  Serial.begin(9600);
  // Conexión WIFI
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED and contconexion <50) { //Cuenta hasta 50 si no se puede conectar lo cancela
    ++contconexion;
    delay(500);
  }
  client.setServer(SERVER, SERVERPORT);
  client.setCallback(callback);
  
}

//--------------------------LOOP--------------------------------
void loop() {

  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  unsigned long currentMillis = millis();
    
   if (Serial.available()) {
    String datos = Serial.readStringUntil('\n');
    if(datos.charAt(0)=='f'){
      datos.remove(0,1);
      separador(datos);
      
      frecuencia.toCharArray(cadena, 10);
      topicoFrecuencia.toCharArray(TOPICO, 25);
      client.publish(TOPICO, cadena);
      
      tension.toCharArray(cadena, 10);
      topicoTension.toCharArray(TOPICO, 25);
      client.publish(TOPICO, cadena);
      
      intensidad.toCharArray(cadena, 10);
      topicoIntensidad.toCharArray(TOPICO, 25);
      client.publish(TOPICO, cadena);
      
      potencia.toCharArray(cadena, 10);
      topicoPotencia.toCharArray(TOPICO, 25);
      client.publish(TOPICO, cadena);
      
      fp.toCharArray(cadena, 10);
      topicoFp.toCharArray(TOPICO, 25);
      client.publish(TOPICO, cadena);
    }
  }
}



void separador(String str){

  unsigned int i=str.indexOf("/");
  frecuencia=str.substring(0,i);
  str.remove(0,i+1);
  i=str.indexOf("/");
  tension=str.substring(0,i);
  str.remove(0,i+1);
  i=str.indexOf("/");
  intensidad=str.substring(0,i);
  str.remove(0,i+1);
  i=str.indexOf("/");
  potencia=str.substring(0,i);
  str.remove(0,i+1);
  i=str.indexOf("/");
  fp=str.substring(0,i);
  str.remove(0,i+1);
}
