# -*- coding: cp1252 -*-
from datetime import date, timedelta
import datetime
import numpy as np
import array
import MySQLdb

f=open('/var/www/html/SDPVA/application/configs/config_python.txt')
config_lines=f.readlines()
host = ''.join(config_lines[1].split('\r\n'))
user = ''.join(config_lines[3].split('\r\n'))
password = ''.join(config_lines[5].split('\r\n'))
database = ''.join(config_lines[7].split('\r\n'))
diretorio_arquivo_entrada = '/var/www/html/SDPVA/RNA/entrada/entrada_altitude.txt'

db = MySQLdb.connect(host=host, user=user, passwd=password, db=database)

cur = db.cursor()
cur2 = db.cursor()
cur3 = db.cursor()

entrada = ""

cur.execute("SELECT datahora, dayofyear(DATE(datahora)) as dia_juliano, HOUR(datahora) FROM processamento;")

row = cur.fetchone()
datahora =str(row[0])
dia_juliano = str(row[1])
hora = str(row[2])

entrada+=hora+" "
entrada+=dia_juliano+" "

cur2.execute("SELECT z, speed, dir, TI, TKE, EDR FROM sodar_atual WHERE data=%s AND (z=30 OR z=60 OR z=90 OR z=120 OR z=150 OR z=180 OR z=210 OR z=240 OR z=270 OR z=300) ORDER BY z;", (datahora))


for row2 in cur2.fetchall() :
	## Para alturas abaixo de 210 metros	
	if row2[0]<210:
		##speed
		if row2[1]==99.99:
			entrada+='5'+" "
		else:
			entrada+=str(row2[1])+" "
		##dir
		if row2[2]==999.9:
			entrada+='120'+" "
		else:
			entrada+=str(row2[2])+" "
		##TI
		if row2[3]==99.99:
			entrada+=('0.37')+" "
		else:
			entrada+=str(row2[3])+" "
		##TKE
		if row2[4]==99.999:
			entrada+='0.2'+" "
		else:
			entrada+=str(row2[4])+" "
		##EDR
		if row2[5]==99.99999:
			entrada+='0.00032'+" "
		else:
			entrada+=str(row2[5])+" "
	##Para alturas maiores que 200, nao pego o valor de TI
	else:
		##speed
		if row2[1]==99.99:
			entrada+='5'+" "
		else:
			entrada+=str(row2[1])+" "
		##dir
		if row2[2]==999.9:
			entrada+='120'+" "
		else:
			entrada+=str(row2[2])+" "
		##TKE
		if row2[4]==99.999:
			entrada+='0.2'+" "
		else:
			entrada+=str(row2[4])+" "
		##EDR
		if row2[5]==99.99999:
			entrada+='0.00032'+" "
		else:
			entrada+=str(row2[5])+" "

##Embutindo valores aleatórios da EMS ao final do arquivo
entrada += "4.1 97 6.42 115 12.7 94 1.07 2.47 5.87 9.58"

print entrada
f = open(diretorio_arquivo_entrada, 'w')
f.write(entrada)
f.close()


