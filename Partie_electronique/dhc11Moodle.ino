//------------------------------------------------------------------------------
//	Librairie : ZLib_CapteurDHT11.ino
//
// One Wire Digital I/O du capteur DHT11
// Appeler la fonction Init_CapDHT11() dans "setup()".
// Appeler la fonction Lect_CapDHT11() dans "loop()".
//
//------------------------------------------------------------------------------


void	Init_CapDHT11(int Num_Pin)
//------------------------------------------------------------------------------
// Parametre en entree : le numero de pin de connexion du capteur 
// Parametre en sortie : la fonction ne retourne rien (déclaration void)
//------------------------------------------------------------------------------
{
	pinMode(Num_Pin, OUTPUT);
	digitalWrite(Num_Pin, HIGH);
	pinMode(Num_Pin, INPUT_PULLUP);
	delay(10);
}

#define	TIME_OUT	200		/* 200 µs */


int		Lect_CapDHT11(int Num_Pin, int pBufRead[])
//------------------------------------------------------------------------------
// Parametre en entree : 1- le numero de pin de connexion du capteur 
//                       2 - le buffer qui va contenir les valeurs lues
// Parametre en sortie : nerr indique s'il y a une erreur de lecture du capteur
//						 nerr = 0; indique pas d'erreur
//							  > 0; indique le numero (type) d'erreur
//------------------------------------------------------------------------------
{
	int nerr, nbyt, nbit, data, idc;		unsigned long dtime;

	//---- Start comm
	pinMode(Num_Pin, OUTPUT);
	digitalWrite(Num_Pin, LOW);			// debut bit de "Start"
	delay(22);							// duree du bit >= 18 ms 
	pinMode(Num_Pin, INPUT_PULLUP);		// pin en entrée
	delayMicroseconds(20);

	//---- Attente ACK du capteur
	dtime = pulseIn(Num_Pin, HIGH, TIME_OUT);
	if (dtime < 40)	{					// Ack = 80 µs High
		nbit = 1;	goto sortie1;
	}

	//---- Lecture des 40 bits = 5 octets de 8 bits 
	for (nbyt = 0; nbyt < 5; nbyt++) {
            data = 0;
            for (nbit = 0; nbit < 8; nbit++) {
                dtime = pulseIn(Num_Pin, HIGH, TIME_OUT);
                if (dtime < 20) { 
                  nerr = 3; goto sortie1; 
                }
                data <<= 1;
                if (dtime > 40) data |= 1;
            }
            pBufRead[nbyt] = data;   // <-- plus de pBufRead++
        }
	//Serial.println("             +++ Lecture Ok ");
	nerr = 0;	goto sortie2;		// Pas d'erreur. Tout est Ok

sortie1:
	//----- Afficher les messages d'erreur
        Serial.println("sortie1:");
	nerr = nbit;	// numero de l'erreur
	if (dtime == 0)
		Serial.print("             +++ Time-Out Lecture  : ");
	else {
		Serial.print("             +++ Pulse trop courte : ");
		Serial.print(dtime);	Serial.print("  ");
		nerr += 1;
	}
	Serial.print(" Seq : ");	Serial.print(nbit);	Serial.println("  ");

sortie2:
        Serial.print("Raw Data: ");
        for (int i = 0; i < 5; i++) {
            Serial.print(pBufRead[i]); Serial.print(" ");
        }
        Serial.println();
	return nerr;
}

bool checksum(const int tab[5]) {
  return ((tab[0] + tab[1] + tab[2] + tab[3]) & 0xFF) == tab[4];
}

void setup()
{
  // put your setup code here, to run once:
  Serial.begin(9600);
  delay(1000);
  int Num_Pin = 30;
  Init_CapDHT11(Num_Pin);

  
}

void loop()
{
  // put your main code here, to run repeatedly:
  delay(2000);
  int Buf[5] = {0};
  int Num_Pin = 30;
  int err = Lect_CapDHT11(Num_Pin, Buf);
  if ( err == 0 && checksum(Buf)){
    Serial.print("Hum : ");
    Serial.print(Buf[0]);          // 湿度整数部分
    Serial.print(" %");
    
    Serial.print("|");
    
    float temp = Buf[2] + Buf[3] * 0.1f;   // 温度 = 整数 + 小数
    Serial.print("Temp : ");
    Serial.print(temp);
    Serial.println(" C");
    Serial.println(" ");

  }else if (err > 0){
    Serial.print("Read date failed, N°err : ");
    Serial.println(err);
    Serial.println(" ");
  }else if (checksum(Buf) == false){
    Serial.println("Checksum wrong !");
    Serial.println(" ");
  }
}
