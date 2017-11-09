
#include <time.h>
#include <stdio.h>


int main()
{
	FILE *entrada;  // entrada = ponteiro do arquivo entrada
 	FILE *saida; // saida = ponteiro do arquivo de saida
	time_t rawtime1, rawtime2, rawtime3;
 	int i;
	int dia, mes, hora, minuto, ano;
	int ent[5]; //Vetor com os valores da data
	struct tm *time_str1; //estrutura necessaria para data 1
 	struct tm *time_str2; //estrutura necessaria para data 2
	struct tm *time_str3; //estrutura necessaria para data 3
	char daybuf1[20]; //String de caracteres para data 1
	char daybuf2[20]; //String de caracteres para data 2
	char daybuf3[20]; //String de caracteres para data 3


	//abrindo o arquivo data_entrada_RNA.txt 
	if ((entrada=fopen("data_entrada_RNA.txt","r")) == NULL)
	{
		printf("Arquivo data_entrada_RNA.txt nao pode ser aberto\n");
		return 0;	
	}
	else
	{
		for(i=0; i<5; i++) //lendo a data de data_entrada_RNA.txt
		{
			fscanf(entrada,"%d", &ent[i]);
			printf("Variavel %d = %d\n ", i, ent[i]);
		}
	}

	fclose(entrada);

	// armazenando os valores de dia,mes,ano,hora e minuto lidos do arquivo data_entrada_RNA.txt em variaveis separadas
	dia = ent[0];
	mes = ent[1];
	ano = ent[2];
	hora = ent[3];
	minuto = ent[4];


	//abrindo o arquivo de saida (data_saida_RNA.txt)
	if ((saida=fopen("data_saida_RNA.txt","w")) == NULL)
 	{
		printf("Arquivo data_saida_RNA.txt nao pode ser aberto\n");
		return 0;	
 	}
 	

	// Calculando a data de 1h a frente baseada na data lida do arquivo data_entrada_RNA.txt

	time_str1 = localtime (&rawtime1);

	time_str1->tm_year = ano - 1900;
	time_str1->tm_mon = mes - 1;
	time_str1->tm_mday = dia;
	time_str1->tm_hour = hora;
	time_str1->tm_min = minuto + 60;
    	time_str1->tm_sec = 1;
   	time_str1->tm_isdst = -1;


	if (mktime(time_str1) == -1) {
		(void)puts("-unknown-");
  	}
	else {
		printf ("\nData 1h: %s\n", asctime (time_str1));
	        strftime(daybuf1, sizeof(daybuf1), "%d %m %Y %H %M \n", time_str1);
		fputs(daybuf1,saida);
  	}


	// Calculando a data de 2h a frente baseada na data lida do arquivo data_entrada_RNA.txt
	time_str2 = localtime (&rawtime2);

	time_str2->tm_year = ano - 1900;
	time_str2->tm_mon = mes - 1;
	time_str2->tm_mday = dia;
	time_str2->tm_hour = hora;
	time_str2->tm_min = minuto + 120;
  	time_str2->tm_sec = 1;
   	time_str2->tm_isdst = -1;

	if (mktime(time_str2) == -1) {
		(void)puts("-unknown-");
  	}
  	else {
		printf ("Data 2h: %s\n", asctime (time_str2));
		strftime(daybuf2, sizeof(daybuf2), "%d %m %Y %H %M \n", time_str2);
	        fputs(daybuf2,saida);
  	}	


	// Calculando a data de 3h a frente baseada na data lida do arquivo data_entrada_RNA.txt
  	time_str3 = localtime (&rawtime3);

	time_str3->tm_year = ano - 1900;
	time_str3->tm_mon = mes - 1;
	time_str3->tm_mday = dia;
	time_str3->tm_hour = hora;
	time_str3->tm_min = minuto + 180;
  	time_str3->tm_sec = 1;
   	time_str3->tm_isdst = -1;

	if (mktime(time_str3) == -1) {
		(void)puts("-unknown-");
  	}
	else {
		printf ("Data 3h: %s\n", asctime (time_str3));
		strftime(daybuf3, sizeof(daybuf3), "%d %m %Y %H %M ", time_str3);
		fputs(daybuf3,saida);
	}


	fclose(saida);

	return 0;
	
}



