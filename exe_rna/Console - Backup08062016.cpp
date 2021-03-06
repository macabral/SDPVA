// Copyright  1997, 1998 by Ward Systems Group, Inc.
//
// Aplicando o NeuroShell Run-Time no Microsoft Visual C++ compiler

/* *************************************************************************************************** */
/*  */
/*  */
/* *************************************************************************************************** */


#include <time.h>
#include <stdio.h>
#include <string.h>
#include "nswrap.h"


//int main()
int __stdcall WinMain(void*, void*, char* cmdLine, int)
{
	FILE *config;  // arquivos de configuracao para caminhos de arquivos de entrada e saida
	FILE *entrada_altitude;  // entrada = ponteiro do arquivo entrada da rede
	FILE *entrada_superficie;  // entrada = ponteiro do arquivo entrada da rede
	FILE *entrada_ws;  // entrada = ponteiro do arquivo entrada da rede
	FILE *saida; // saida = ponteiro do arquivo de saida da rede
	FILE *arq_log; // saida = ponteiro do arquivo de log da rede
	int a,b;
	long ret1, ret2, ret3, ret4, ret5, ret6, ret7, ret8, ret9, ret10, ret11, ret12, ret13, ret14, ret15, ret16, numinputs, numoutputs, i;
	long  ret17, ret18, ret19, ret20, ret21, ret22, ret23, ret24, ret25, ret26, ret27, ret28, ret29, ret30, ret31, ret32, ret33, ret34, ret35, ret36;
	//int temp_nova[2]; //Vetor com os valores da data 
	double ina_vento[58]; //Numero de entradas da Rede 
	double ina_ws[92]; //Numero de entradas da Rede 
	double outa[1];// Saidas de todas as redes
	char configs1[100];//diretorios de configuracao
	char configs2[100];//diretorios de configuracao
	char configs3[100];//diretorios de configuracao
	char configs4[100];//diretorios de configuracao
	char configs5[100];//diretorios de configuracao
	char configs6[100];//diretorios de configuracao
	
	//Lendo arquivo de configuracao
	config = fopen("C:/RNA/config_RNA.txt", "r");
	fscanf(config, "%s %s %s %s %s %s", &configs1, &configs2, &configs3, &configs4, &configs5, &configs6);
	//arq_log = fopen(configs3, "w");
	//fprintf(arq_log, "%s\n %s\n %s\n %s\n %s\n %s\n ", configs1, configs2, configs3, configs4, configs5, configs6);

	//entrada_altitude = fopen(configs4, "r");

	//for (a = 0; a<48; a++) //lendo o arquivo entrada.txt e escrevendo no vetor de entrada da rede
	//{
	//	fscanf(entrada_altitude, "%lf", &ina_vento[a]);
	//}

	//entrada_superficie = fopen(configs5, "r");
	//for (a = 48; a<58; a++) //lendo o arquivo entrada.txt e escrevendo no vetor de entrada da rede
	//{
	//	fscanf(entrada_superficie, "%lf", &ina_vento[a]);
	//}

	//for (a = 0; a<58; a++)
	//{
	//	//printf("Variavel %d = %g\n ", a, ina[a]);
	//	fprintf(arq_log, "Variavel %d = %g\n ", a, ina_vento[a]); //escrevendo no arquivo log_rna.txt 
	//}
	

	

	//Criando ponteiros para as redes neurais
	//###################################################################################################################
	char *fname1 = "C:/RNA/RNA_100_dir15.net"; //Arquivo da Rede direcao do vento - 100 pes 15 minutos
	char *fname2 = "C:/RNA/RNA_100_dir30.net"; //Arquivo da Rede direcao do vento - 100 pes 30 minutos
	char *fname3 = "C:/RNA/RNA_100_dir45.net"; //Arquivo da Rede direcao do vento - 100 pes 45 minutos

	char *fname4 = "C:/RNA/RNA_100_vel15.net"; //Arquivo da Rede velocidade do vento - 100 pes 15 minutos
	char *fname5 = "C:/RNA/RNA_100_vel30.net"; //Arquivo da Rede velocidade do vento - 100 pes 30 minutos
	char *fname6 = "C:/RNA/RNA_100_vel45.net"; //Arquivo da Rede velocidade do vento - 100 pes 45 minutos

	char *fname7 = "C:/RNA/RNA_200_dir15.net"; //Arquivo da Rede direcao do vento - 200 pes 15 minutos
	char *fname8 = "C:/RNA/RNA_200_dir30.net"; //Arquivo da Rede direcao do vento - 200 pes 30 minutos
	char *fname9 = "C:/RNA/RNA_200_dir45.net"; //Arquivo da Rede direcao do vento - 200 pes 45 minutos

	char *fname10 = "C:/RNA/RNA_200_vel15.net"; //Arquivo da Rede velocidade do vento - 200 pes 15 minutos
	char *fname11 = "C:/RNA/RNA_200_vel30.net"; //Arquivo da Rede velocidade do vento - 200 pes 30 minutos
	char *fname12 = "C:/RNA/RNA_200_vel45.net"; //Arquivo da Rede velocidade do vento - 200 pes 45 minutos

	char *fname13 = "C:/RNA/RNA_300_dir15.net"; //Arquivo da Rede direcao do vento - 300 pes 15 minutos
	char *fname14 = "C:/RNA/RNA_300_dir30.net"; //Arquivo da Rede direcao do vento - 300 pes 30 minutos
	char *fname15 = "C:/RNA/RNA_300_dir45.net"; //Arquivo da Rede direcao do vento - 300 pes 45 minutos

	char *fname16 = "C:/RNA/RNA_300_vel15.net"; //Arquivo da Rede velocidade do vento - 300 pes 15 minutos
	char *fname17 = "C:/RNA/RNA_300_vel30.net"; //Arquivo da Rede velocidade do vento - 300 pes 30 minutos
	char *fname18 = "C:/RNA/RNA_300_vel45.net"; //Arquivo da Rede velocidade do vento - 300 pes 45 minutos

	char *fname19 = "C:/RNA/RNA_sup_dir15.net"; //Arquivo da Rede direcao do vento - superficie 15 minutos
	char *fname20 = "C:/RNA/RNA_sup_dir30.net"; //Arquivo da Rede direcao do vento - superficie 30 minutos
	char *fname21 = "C:/RNA/RNA_sup_dir45.net"; //Arquivo da Rede direcao do vento - superficie 45 minutos

	char *fname22 = "C:/RNA/RNA_sup_vel15.net"; //Arquivo da Rede velocidade do vento - superficie 15 minutos
	char *fname23 = "C:/RNA/RNA_sup_vel30.net"; //Arquivo da Rede velocidade do vento - superficie 30 minutos
	char *fname24 = "C:/RNA/RNA_sup_vel45.net"; //Arquivo da Rede velocidade do vento - superficie 45 minutos 

	char *fname25 = "C:/RNA/RNA_WS_100_15.net"; //Arquivo de WS - 100 pes 15 minutos
	char *fname26 = "C:/RNA/RNA_WS_100_30.net"; //Arquivo de WS - 100 pes 30 minutos
	char *fname27 = "C:/RNA/RNA_WS_100_45.net"; //Arquivo de WS - 100 pes 45 minutos

	char *fname28 = "C:/RNA/RNA_WS_200_15.net"; //Arquivo de WS - 200 pes 15 minutos
	char *fname29 = "C:/RNA/RNA_WS_200_30.net"; //Arquivo de WS - 200 pes 30 minutos
	char *fname30 = "C:/RNA/RNA_WS_200_45.net"; //Arquivo de WS - 200 pes 45 minutos

	char *fname31 = "C:/RNA/RNA_WS_300_15.net"; //Arquivo de WS - 300 pes 15 minutos
	char *fname32 = "C:/RNA/RNA_WS_300_30.net"; //Arquivo de WS - 300 pes 30 minutos
	char *fname33 = "C:/RNA/RNA_WS_300_45.net"; //Arquivo de WS - 300 pes 45 minutos

	char *fname34 = "C:/RNA/RNA_WS_sup_15.net"; //Arquivo de WS - superficie 15 minutos
	char *fname35 = "C:/RNA/RNA_WS_sup_30.net"; //Arquivo de WS - superficie pes 30 minutos
	char *fname36 = "C:/RNA/RNA_WS_sup_45.net"; //Arquivo de WS - superficie pes 45 minutos
	//###################################################################################################################

	fprintf(arq_log, "Rede 1 : %s", fname1);

	

	float enhgen; //Valor algoritmo genetico (padrao = 0.5) 	
	
	saida = fopen(configs2, "w");
	arq_log = fopen(configs3, "w");
	fprintf(arq_log, "%s \n", "LOG RNA "); //escrevendo no arquivo log_rna_teste.txt
	//fprintf(arq_log, "%s\n %s\n %s\n %s\n %s\n %s\n ", configs1, configs2, configs3, configs4, configs5, configs6);

	
	

	
	//######################################################################################################################
	//abrindo o arquivo entrada_altitude.txt 
	if ((entrada_altitude = fopen(configs4, "r")) == NULL)
	{
		//printf("Arquivo nao pode ser aberto\n");
		fprintf(arq_log, "%s \n", "Arquivo entrada_altitude.txt nao pode ser aberto\n "); //escrevendo no arquivo log_rna.txt
		return 0;
	}
	else
	{
		fprintf(arq_log, "\n%s \n", "Variaveis de data...\n "); //escrevendo no arquivo log_rna.txt

		for (a = 0; a<48; a++) //lendo o arquivo entrada.txt e escrevendo no vetor de entrada da rede
		{
			fscanf(entrada_altitude, "%lf", &ina_vento[a]);
		}

	}
	fclose(entrada_altitude);

	//abrindo o arquivo entrada_superficie.txt 
	if ((entrada_superficie = fopen(configs5, "r")) == NULL)
	{
		//printf("Arquivo nao pode ser aberto\n");
		fprintf(arq_log, "%s \n", "Arquivo entrada_superficie.txt nao pode ser aberto\n "); //escrevendo no arquivo log_rna.txt
		return 0;
	}
	else
	{
		fprintf(arq_log, "\n%s \n", "Variaveis de data...\n "); //escrevendo no arquivo log_rna.txt

		for (a = 48; a<58; a++) //lendo o arquivo entrada.txt e escrevendo no vetor de entrada da rede
		{
			fscanf(entrada_superficie, "%lf", &ina_vento[a]);
		}

	}
	fclose(entrada_superficie);

	//abrindo o arquivo entrada_ws.txt 
	if ((entrada_ws = fopen(configs6, "r")) == NULL)
	{
		//printf("Arquivo nao pode ser aberto\n");
		fprintf(arq_log, "%s \n", "Arquivo entrada_ws.txt nao pode ser aberto\n "); //escrevendo no arquivo log_rna.txt
		return 0;
	}
	else
	{
		fprintf(arq_log, "\n%s \n", "Variaveis de data...\n "); //escrevendo no arquivo log_rna.txt

		for (a = 0; a<92; a++) //lendo o arquivo entrada.txt e escrevendo no vetor de entrada da rede neural
		{
			fscanf(entrada_ws, "%lf", &ina_ws[a]);
		}

	}
	fclose(entrada_altitude);

	//  imprimindo a entrada_altitude
	printf("Valores de entrada:\n ");
	//fprintf(arq_log, "\n%s \n", "Variaveis de entrada da NOVA rede...\n "); //escrevendo no arquivo log_rna.txt
	for (a = 0; a<58; a++)
	{
		//printf("Variavel %d = %g\n ", a, ina[a]);
		fprintf(arq_log, "Variavel %d = %g\n ", a, ina_vento[a]); //escrevendo no arquivo log_rna.txt 
	}

	

	//------INICIO DA REDE 1A----------------------------------------------------------------
	ret1 = OpenNetwork(fname1, &numinputs, &numoutputs, &enhgen);

	if (ret1 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede com o valor de Generalizacao Padrao
		ret1 = FireNetwork(ina_vento, outa, &enhgen);
			fprintf(arq_log, "Direcao vento 15 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
			fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname1); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret1 = CloseNetwork();

	//------INICIO DA REDE 2A----------------------------------------------------------------
	ret2 = OpenNetwork(fname2, &numinputs, &numoutputs, &enhgen);

	if (ret2 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret2 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 30 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname2); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret2 = CloseNetwork();

	//------INICIO DA REDE 3A----------------------------------------------------------------
	ret3 = OpenNetwork(fname3, &numinputs, &numoutputs, &enhgen);

	if (ret3 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret3 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 45 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname3); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret3 = CloseNetwork();

	//------INICIO DA REDE 4A----------------------------------------------------------------
	ret4 = OpenNetwork(fname4, &numinputs, &numoutputs, &enhgen);

	if (ret4 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret4 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 15 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname4); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret4 = CloseNetwork();

	//------INICIO DA REDE 5A----------------------------------------------------------------
	ret5 = OpenNetwork(fname5, &numinputs, &numoutputs, &enhgen);

	if (ret5 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret5 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 30 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname5); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret5 = CloseNetwork();

	//------INICIO DA REDE 6A----------------------------------------------------------------
	ret6 = OpenNetwork(fname6, &numinputs, &numoutputs, &enhgen);

	if (ret6 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret6 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 45 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname6); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret6 = CloseNetwork();

	//------INICIO DA REDE 7A----------------------------------------------------------------
	ret7 = OpenNetwork(fname7, &numinputs, &numoutputs, &enhgen);

	if (ret7 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret7 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 15 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname7); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret7 = CloseNetwork();

	//------INICIO DA REDE 8A----------------------------------------------------------------
	ret8 = OpenNetwork(fname8, &numinputs, &numoutputs, &enhgen);

	if (ret8 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret8 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 30 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname8); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret8 = CloseNetwork();

	//------INICIO DA REDE 9A----------------------------------------------------------------
	ret9 = OpenNetwork(fname9, &numinputs, &numoutputs, &enhgen);

	if (ret9 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret9 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 45 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname9); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret9 = CloseNetwork();

	//------INICIO DA REDE 10A----------------------------------------------------------------
	ret10 = OpenNetwork(fname10, &numinputs, &numoutputs, &enhgen);

	if (ret10 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret10 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 15 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname10); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret10 = CloseNetwork();

	//------INICIO DA REDE 11A----------------------------------------------------------------
	ret11 = OpenNetwork(fname11, &numinputs, &numoutputs, &enhgen);

	if (ret11 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret11 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 30 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname11); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret11 = CloseNetwork();

	//------INICIO DA REDE 12A----------------------------------------------------------------
	ret12 = OpenNetwork(fname12, &numinputs, &numoutputs, &enhgen);

	if (ret12 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret12 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 45 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname12); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret12 = CloseNetwork();

	//------INICIO DA REDE 13A----------------------------------------------------------------
	ret13 = OpenNetwork(fname13, &numinputs, &numoutputs, &enhgen);

	if (ret13 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret13 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 15 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname13); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret13 = CloseNetwork();

	//------INICIO DA REDE 14A----------------------------------------------------------------
	ret14 = OpenNetwork(fname14, &numinputs, &numoutputs, &enhgen);

	if (ret14 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret14 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 30 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname14); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret14 = CloseNetwork();

	//------INICIO DA REDE 15A----------------------------------------------------------------
	ret15 = OpenNetwork(fname15, &numinputs, &numoutputs, &enhgen);

	if (ret15 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret15 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 45 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname15); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret15 = CloseNetwork();

	//------INICIO DA REDE 16A----------------------------------------------------------------
	ret16 = OpenNetwork(fname16, &numinputs, &numoutputs, &enhgen);

	if (ret16 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret16 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 15 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname16); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret16 = CloseNetwork();

	//------INICIO DA REDE 17A----------------------------------------------------------------
	ret17 = OpenNetwork(fname17, &numinputs, &numoutputs, &enhgen);

	if (ret17 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret17 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 30 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname17); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret17 = CloseNetwork();

	//------INICIO DA REDE 18A----------------------------------------------------------------
	ret18 = OpenNetwork(fname18, &numinputs, &numoutputs, &enhgen);

	if (ret18 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret18 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 45 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname18); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret18 = CloseNetwork();

	//------INICIO DA REDE 19A----------------------------------------------------------------
	ret19 = OpenNetwork(fname19, &numinputs, &numoutputs, &enhgen);

	if (ret19 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret19 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 15 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname19); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret19 = CloseNetwork();

	//------INICIO DA REDE 20A----------------------------------------------------------------
	ret20 = OpenNetwork(fname20, &numinputs, &numoutputs, &enhgen);

	if (ret20 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret20 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 30 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname20); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret20 = CloseNetwork();

	//------INICIO DA REDE 21A----------------------------------------------------------------
	ret21 = OpenNetwork(fname21, &numinputs, &numoutputs, &enhgen);

	if (ret21 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret21 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Direcao vento 45 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname21); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret21 = CloseNetwork();

	//------INICIO DA REDE 22A----------------------------------------------------------------
	ret22 = OpenNetwork(fname22, &numinputs, &numoutputs, &enhgen);

	if (ret22 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret22 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 15 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname22); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret22 = CloseNetwork();

	//------INICIO DA REDE 23A----------------------------------------------------------------
	ret23 = OpenNetwork(fname23, &numinputs, &numoutputs, &enhgen);

	if (ret23 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret23 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 30 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname23); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret23 = CloseNetwork();

	//------INICIO DA REDE 24A----------------------------------------------------------------
	ret24 = OpenNetwork(fname24, &numinputs, &numoutputs, &enhgen);

	if (ret24 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret24 = FireNetwork(ina_vento, outa, &enhgen);
		fprintf(arq_log, "Velocidade vento 45 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname24); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret24 = CloseNetwork();

	//------INICIO DA REDE 25A----------------------------------------------------------------
	ret25 = OpenNetwork(fname25, &numinputs, &numoutputs, &enhgen);

	if (ret25 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret25 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 15 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname25); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret25 = CloseNetwork();

	//------INICIO DA REDE 26A----------------------------------------------------------------
	ret26 = OpenNetwork(fname26, &numinputs, &numoutputs, &enhgen);

	if (ret26 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret26 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 30 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname26); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret26 = CloseNetwork();

	//------INICIO DA REDE 27A----------------------------------------------------------------
	ret27 = OpenNetwork(fname27, &numinputs, &numoutputs, &enhgen);

	if (ret27 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret27 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 45 minutos 100 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname27); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret27 = CloseNetwork();

	//------INICIO DA REDE 28A----------------------------------------------------------------
	ret28 = OpenNetwork(fname28, &numinputs, &numoutputs, &enhgen);

	if (ret28 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret28 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 15 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname28); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret28 = CloseNetwork();

	//------INICIO DA REDE 29A----------------------------------------------------------------
	ret29 = OpenNetwork(fname29, &numinputs, &numoutputs, &enhgen);

	if (ret29 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret29 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 30 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname29); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret29 = CloseNetwork();

	//------INICIO DA REDE 30A----------------------------------------------------------------
	ret30 = OpenNetwork(fname30, &numinputs, &numoutputs, &enhgen);

	if (ret30 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret30 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 45 minutos 200 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname30); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret30 = CloseNetwork();

	//------INICIO DA REDE 31A----------------------------------------------------------------
	ret31 = OpenNetwork(fname31, &numinputs, &numoutputs, &enhgen);

	if (ret31 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret31 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 15 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname31); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret31 = CloseNetwork();

	//------INICIO DA REDE 32A----------------------------------------------------------------
	ret32 = OpenNetwork(fname32, &numinputs, &numoutputs, &enhgen);

	if (ret32 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret32 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 30 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname32); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret32 = CloseNetwork();

	//------INICIO DA REDE 33A----------------------------------------------------------------
	ret33 = OpenNetwork(fname33, &numinputs, &numoutputs, &enhgen);

	if (ret33 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret33 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 45 minutos 300 pes = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname33); //escrevendo no arquivo log_rna.txt
	}
	ret33 = CloseNetwork();

	//------INICIO DA REDE 34A----------------------------------------------------------------
	ret34 = OpenNetwork(fname34, &numinputs, &numoutputs, &enhgen);

	if (ret34 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret34 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 15 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname34); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret34 = CloseNetwork();

	//------INICIO DA REDE 35A----------------------------------------------------------------
	ret35 = OpenNetwork(fname35, &numinputs, &numoutputs, &enhgen);

	if (ret35 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret35 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 30 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname35); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret35 = CloseNetwork();

	//------INICIO DA REDE 36A----------------------------------------------------------------
	ret36 = OpenNetwork(fname36, &numinputs, &numoutputs, &enhgen);

	if (ret36 == 0) //Rede aberta com sucesso
	{
		//Rodando a rede
		ret36 = FireNetwork(ina_ws, outa, &enhgen);
		fprintf(arq_log, "WS 45 minutos superficie = %lf\n ", outa[0]); //escrevendo no arquivo log_rna.txt 
		fprintf(saida, "%g ", outa[0]);

	}
	else {
		//printf("Erro abrindo a rede.\n");
		fprintf(arq_log, "%s %s\n", "Erro abrindo NOVA rede... ", fname36); //escrevendo no arquivo log_rna.txt
	}
	//Liberando memoria. 
	ret36 = CloseNetwork();

	fclose(saida);
	
	fclose(arq_log);
	fclose(config);

	//####################################################################################################
	
	
}
