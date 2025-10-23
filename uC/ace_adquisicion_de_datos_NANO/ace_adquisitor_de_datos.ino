// Definición de las variables.
#define muestras 200 // 200 muestras equivalen a 10 ciclos completos de la señal.
#define Vref 2.3500//tension de referencia de la placa del preset.
int voltaje[muestras];
int intensidad[muestras];
unsigned long tiempoMuestreo=0;
float vRMS=0;
float iRMS=0;
float vPromedio=0;
float iPromedio=0;
float P=0;
double FP=0;
volatile unsigned long lastMillisT=0;
volatile unsigned long lastMillisC=0;
volatile int crucesCero=0;
volatile boolean flagTension=LOW;
volatile boolean flagCorriente=LOW;
float F=0;

void setup() {
// inicialización del puerto serie.
Serial.begin(9600);
pinMode(2,INPUT);
pinMode(3,INPUT);
}
void loop() {
  valoresPromedio();
  frecuencia();
  if(iPromedio>0.1){
    factorPotencia();
    potencia();
  }
  else{
    iPromedio=0;
    P=0;
    FP=0;
  }
  envio();
}



/*******************
 * FUNCIONES*
 ******************/
void tomaDatos(){
  unsigned long inicio=0;
  for(int i=0; i<muestras; i++){
    voltaje[i] = analogRead(A1); //se muestrea a 1 kHz.
    intensidad[i] = analogRead(A0);
    delayMillis(1);
  }
}

void tensionEficaz(){
  float sumatoriaCuadrados=0;
  for(int i=0;i<muestras;i++){
    sumatoriaCuadrados+=pow(((voltaje[i]*0.0048)-Vref),2);
  }
  vRMS=sqrt(sumatoriaCuadrados/muestras)*142.86;
}

void corrienteEficaz(){
  float sumatoriaCuadrados=0;
  for(int i=0;i<muestras;i++){
    sumatoriaCuadrados+=pow(((intensidad[i]*0.0048)-2.5000),2);
  }
  iRMS=sqrt(sumatoriaCuadrados/muestras)/0.0660;
}

 
void valoresPromedio(void){
  float sumaV=0;
  float sumaI=0;
  for(int i=0;i<10;i++){
    tomaDatos();
    tensionEficaz();
    corrienteEficaz();
    sumaV+=vRMS;
    sumaI+=iRMS;
  }
  vPromedio=sumaV/10.0000;
  iPromedio=sumaI/10.0000;
  vPromedio=(vPromedio*1.0645)-14.0965;//Calibracion del sensor de tension. orig. 18.0965
  iPromedio=(iPromedio*1.1304)-0.7574;//Calibracion del sensor de corriente.
}

void frecuencia(){
  unsigned long inicio=0;
  float freq=0;
  float periodoSenal=0;
  unsigned long tiempoMuestreo=0;
  crucesCero=0;
  attachInterrupt(digitalPinToInterrupt(2),ISR_cruceTension,RISING);
  while(crucesCero<51){
    if(crucesCero==1){//La condicion contador==0 es para detectar el primer cruce y guardar el tiempo de inicio.
      inicio=micros();
    }
  }
  detachInterrupt(digitalPinToInterrupt(2));
  tiempoMuestreo=micros()-inicio;//tiempoMuestreo es global porque la utilizo tambien en tomaDatos().
  periodoSenal=tiempoMuestreo/(crucesCero-1.00);
  F=1/(periodoSenal*pow(10,-6));
  }

void factorPotencia(){
  unsigned long t1;
  unsigned long t2;
  unsigned long tiempoFp;
  unsigned long inicio=0;
  boolean tensionPrimero=LOW;
  float rad=0;
  attachInterrupt(digitalPinToInterrupt(2),ISR_cruceTension,RISING);
  attachInterrupt(digitalPinToInterrupt(3),ISR_cruceCorriente,RISING);
  delay(120);
  flagTension=LOW;
  flagCorriente=LOW;
  tensionPrimero=LOW;
  while(flagTension==LOW&&flagCorriente==LOW){}
  inicio=micros();
  if(flagTension==HIGH&&flagCorriente==LOW){
    tensionPrimero=HIGH;
    flagTension=LOW;
    while(flagCorriente==LOW){}
    t1=micros()-inicio;
    inicio=micros();
    while(flagTension==LOW){}
    t2=micros()-inicio;
  }
  else{
    if(flagTension==LOW&&flagCorriente==HIGH){
      flagCorriente=LOW;
      while(flagTension==LOW){}
      t1=micros()-inicio;
      inicio=micros();
      while(flagCorriente==LOW){}
      t2=micros()-inicio;
    }
    else{
      tiempoFp=0;
    }
  }
  if(t1<t2){
    tiempoFp=t1;
  }
  else{
    tiempoFp=t2;
  }
  rad=tiempoFp*pow(10,-6)*314.16;
  FP=cos(rad);
  if(tensionPrimero==HIGH){
    if(t2<t1){
      FP=FP*(-1.00);
    }
  }
  else{
    if(t1<t2){
     FP=FP*(-1.00);
    }
  }
  detachInterrupt(digitalPinToInterrupt(2));
  detachInterrupt(digitalPinToInterrupt(3));
}

void potencia(void){
  P=vPromedio*iPromedio*abs(FP);
}


void envio(){
  Serial.print("frecuencia: ");
  Serial.print(F,1);
  Serial.print("       Tension: ");
  Serial.print(vPromedio,2);
  Serial.print("       Corriente: ");
  Serial.print(iPromedio,2);
  Serial.print("       Potencia: ");
  Serial.print(P,0);
  Serial.print("       FP: ");
  if(FP==0){
      Serial.print("-");
  }
  else{
    Serial.print(abs(FP),2);
  }
  if(FP>0 && FP<=0.99){
    Serial.println("   Inductivo");
  }
  else{
    if(FP<0 && FP>=-0.99){
      Serial.println("    Capacitivo");
    }
    else{
      if(FP==0){
        Serial.println("");
      }
      else{
        Serial.println("    Resistivo");
      }
    }
  }
}

void delayMillis(int t1){
  unsigned long t;
  t=millis();
  while(millis()-t<t1){}
}

void delayMicros(unsigned long t1){
  unsigned long t;
  t=micros();
  while(micros()-t<t1){}
}

/*****************
 * INTERRUPCIONES*
 ****************/
void ISR_cruceTension(){
  if(millis()>lastMillisT+12){//Filtro para el ruido de la señal.
    lastMillisT=millis();
    //TODO
    crucesCero++;
    flagTension=HIGH;
  }
}

void ISR_cruceCorriente(){
  if(millis()>lastMillisC+12){//Filtro para el ruido de la señal.
    lastMillisC=millis();
    //TODO
    flagCorriente=HIGH;
  }
}
