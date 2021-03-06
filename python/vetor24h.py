# -*- coding: cp1252 -*-
'''
Demonstration of wind barb plots
'''

import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from matplotlib import colors
from matplotlib import mpl
from matplotlib.backends.backend_agg import FigureCanvasAgg as FigureCanvas
from matplotlib.figure import Figure
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
diretorio_graficos = ''.join(config_lines[9].split('\r\n'))

##var = raw_input("Please enter something: ")
##print "you entered", var

db = MySQLdb.connect(host=host, user=user, passwd=password, db=database)


cur = db.cursor() 
cur2 = db.cursor()

##SELECT s.id, s.data, s.z, s.speed, s.dir from sodar_ncqar s where s.data > DATE_SUB(NOW(), INTERVAL 1455 MINUTE) AND s.data < NOW() AND s.z <=520 order by s.data DESC, s.z DESC

## Escrevendo SQL's
##cur.execute("SELECT * FROM sodar_ncqar order by id desc limit 1250")
##cur.execute("SELECT * FROM sodar_atual s where s.z<200 AND s.u>4 AND s.v>4 AND s.u<20 AND s.v<20 ORDER BY s.data  DESC LIMIT 300")
cur.execute("SELECT s.id, s.data, s.z, s.speed, s.dir, s.U, s.V from sodar_atual s where s.data > DATE_SUB(NOW(), INTERVAL 1455 MINUTE) AND s.data < NOW() AND s.z <=520 order by s.data DESC, s.z DESC")
##cur2.execute("SELECT DISTINCT DATE_FORMAT(s.data,'%H:%i') FROM sodar_ncqar s ORDER BY id DESC LIMIT 24")
cur2.execute("SELECT wind_int_valor_alt_critica_1, wind_int_valor_alt_critica_2, wind_int_valor_alt_critica_3, wind_int_valor_alt_critica_4, wind_int_nome_alt_critica_1,wind_int_nome_alt_critica_2,wind_int_nome_alt_critica_3,wind_int_nome_alt_critica_4 FROM parametros")

for row2 in cur2.fetchall() :
    altitude1 = row2[0]
    altitude2 = row2[1]
    altitude3 = row2[2]
    altitude4 = row2[3]
    nome_altitude1 = row2[4]
    nome_altitude2 = row2[5]
    nome_altitude3 = row2[6]
    nome_altitude4 = row2[7]
	
datau = [] #Componente U
datav = [] #Componente V
datax = [] #Coordenada X
datay = [] #Coordenada Y
datas = [] #Vetor de velocidades
xticks = [] #Vetor de Legendas para o eixo X
cor = [] #Vetor de cores das barbelas

##datax = (1,2,3,4,5,6,7,8,9,10)
##datay = (1,2,3,4,5,6,7,8,9,10)

x = 97 ## N�mero de observa��es com intervalo de 15 minutos para 24 horas 
a = 50 ## N�mero de alturas , indo de 30 a 520 temos 50 alturas
y = 97 ## Numero de observacoes com intervalo de 15 minutos para 24 horas 
z = 530 ## Altura m�xima acrescida de 10 unidades


hora_obs_atual = []

for hora_obs in range(0,x,1):
    hora_atual_grafico = datetime.datetime.now() - datetime.timedelta(seconds=hora_obs*900)
    if (hora_atual_grafico.minute >=00 and hora_atual_grafico.minute <15):
        minutos_atual = '00'
    if (hora_atual_grafico.minute >=15 and hora_atual_grafico.minute <30):
        minutos_atual = '15'
    if (hora_atual_grafico.minute >=30 and hora_atual_grafico.minute <45):
        minutos_atual = '30'
    if (hora_atual_grafico.minute >=45 and hora_atual_grafico.minute <60):
        minutos_atual = '45'
    for n in range(0,50,1):
        hora_obs_atual.append(hora_atual_grafico.strftime('%Y-%m-%d %H:') + minutos_atual + ':00')


for i in range(0,x,1):
    for l in range(a):
        datax.append(i)
    
for j in range(y):
    for k in range(30,z,10):
        datay.append(k)

contador = 0
## print all the first cell of all the rows
for row in cur.fetchall() :
    if contador >=len(hora_obs_atual):
            break
    if str(row[1])== str(hora_obs_atual[contador]):
        if (row[5]== 99.99):
            datau.append(0) 
        else:
            datau.append(row[5]*(-1))
        if (row[6]== 99.99):
            datav.append(0) 
        else:
            datav.append(row[6]*(-1))
        ##Adicionando lista velocidade    
        datas.append(row[3])
        contador +=1
        
    else:
        while str(row[1]) != str(hora_obs_atual[contador]):
            datau.append(0)
            datav.append(0)
            contador +=1
            if contador >=len(hora_obs_atual):
                break
        if contador >=len(hora_obs_atual):
            break
        if (row[5]== 99.99):
            datau.append(0) 
        else:
            datau.append(row[5]*(-1))
        if (row[6]== 99.99):
            datav.append(0) 
        else:
            datav.append(row[6]*(-1))
        ##Adicionando lista velocidade    
        datas.append(row[3])
        contador +=1

hora_atual = datetime.datetime.now()
if (hora_atual.minute >=00 and hora_atual.minute <15):
    minutos = '00'
if (hora_atual.minute >=15 and hora_atual.minute <30):
    minutos = '15'
if (hora_atual.minute >=30 and hora_atual.minute <45):
    minutos = '30'
if (hora_atual.minute >=45 and hora_atual.minute <60):
    minutos = '45'
    
for hora in range(0,25,1):
    hora_atual = datetime.datetime.now() - datetime.timedelta(seconds=hora*3600)
    horario = str(hora_atual.hour) + ":" + minutos
    xticks.append(horario)

##Adicionando label de horas 
##for row2 in cur2.fetchall() :
  ##  xticks.append(row2[0])


## Invertendo todos os vetores
xticks.reverse()
datau.reverse()
datav.reverse()
datas.reverse()
   
for speed in range(len(datas)) :
    if (datas[speed]== 99.99):
        cor.append('#FFDAB9')
    elif ((datas[speed]>0) and (datas[speed]<1)):
        cor.append('#000000')
    elif ((datas[speed]>=1) and (datas[speed]<2)):
        cor.append('#00FFE1')
    elif ((datas[speed]>=2) and (datas[speed]<3)):
        cor.append('#02FEAB')
    elif ((datas[speed]>=3) and (datas[speed]<4)):
        cor.append('#0CF774')
    elif ((datas[speed]>=4) and (datas[speed]<5)):
        cor.append('#07FB3B')
    elif ((datas[speed]>=5) and (datas[speed]<6)):
        cor.append('#03FD05')
    elif ((datas[speed]>=6) and (datas[speed]<7)):
        cor.append('#39FC0A')
    elif ((datas[speed]>=7) and (datas[speed]<8)):
        cor.append('#6EF707')
    elif ((datas[speed]>=8) and (datas[speed]<9)):
        cor.append('#ADFD06')
    elif ((datas[speed]>=9) and (datas[speed]<10)):
        cor.append('#E7FE00')
    elif ((datas[speed]>=10) and (datas[speed]<11)):
        cor.append('#FEED07')
    elif ((datas[speed]>=11) and (datas[speed]<12)):
        cor.append('#FBBA08')
    elif ((datas[speed]>=12) and (datas[speed]<13)):
        cor.append('#FC880D')
    elif ((datas[speed]>=13) and (datas[speed]<14)):
        cor.append('#FF5D00')
    elif ((datas[speed]>=14) and (datas[speed]<15)):
        cor.append('#FF2700')
    elif ((datas[speed]>=15) and (datas[speed]<16)):
        cor.append('#F80207')
    elif ((datas[speed]>=16) and (datas[speed]<17)):
        cor.append('#F7023F')
    elif ((datas[speed]>=17) and (datas[speed]<18)):
        cor.append('#FB066E')
    elif ((datas[speed]>=18) and (datas[speed]<19)):
        cor.append('#FB03A2')
    elif ((datas[speed]>=19) and (datas[speed]<20)):
        cor.append('#FA04D5')
    elif (datas[speed]>=20):
        cor.append('#CE1BCF')
        
masked_u = np.ma.masked_array(datau)
for b in range(len(datau)) :
    if ((datau[b] == 0) and (datav[b] == 0)):
       ##datablank.append(0)
       masked_u[b] = 1000
       masked_u[b] = np.ma.masked
    else :
       masked_u[b] = datau[b] 

        
##print datau
##print datav
print datax
print datay
print datas
print xticks
print masked_u


#---------------------------- Pode ser �til no futuro ----------------------------------------

#ax.xaxis.set_ticks([1.,2.,3.,10.])



#---------------------------------------------------------------------------------------------
# (coordenada de x para a primeira legenda, coordenada de x para a �ltima legenda, numero de legendas de x)
x = np.linspace(0, 96, 25)
y = np.linspace(30,510, 9)

X,Y = np.meshgrid(x, x)
U, V = 5*X, 5*Y

yticks = ('', 300, 500, '', 900, 1100, 1300, 1500, 1700)

#fig = plt.imshow(100)
#ax2 = fig.add_subplot(111)
#mn=5       # colorbar min value
#mx=15         # colorbar max value
#md=50                  # colorbar midpoint value
#cbar=plt.colorbar()              # the mystery step ???????????
#cbar.set_ticks([mn,md,mx])
#cbar.set_ticklabels([mn,md,mx])

#Change colors as well as the increments for parts of the barbs
fig = plt.figure()
ax = plt.subplot(1,1,1)
##barbela = ax.barbs(datax, datay, masked_u, datav, flagcolor='r', length=7,
##   barbcolor=cor, barb_increments=dict(half=1.5, full=4, flag=24),
##   flip_barb=True,pivot='middle', sizes=dict(emptybarb=0.1, spacing=0.2, height=0.2))


vetor = ax.quiver(datax, datay, masked_u, datav, color=cor, minshaft=2)
#qk = ax.quiverkey(vetor,datax,datay, 7, 'vento')
ax.yaxis.grid(color='#B0C4DE', linestyle='dashed')
ax.xaxis.grid(color='#B0C4DE', linestyle='dashed')

plt.axhline(altitude1*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude2*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude3*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude4*0.305, color='#551A8B', zorder=-3, linestyle='dashed')

ax.text(-4.2, altitude1*0.305, nome_altitude1, rotation=30, fontsize=9, color='#551A8B')
ax.text(-4.2, altitude2*0.305 , nome_altitude2, rotation=30, fontsize=9, color='#551A8B')
ax.text(-4.2, altitude3*0.305 , nome_altitude3, rotation=30, fontsize=9, color='#551A8B')
ax.text(-4.2, altitude4*0.305 , nome_altitude4, rotation=30, fontsize=9, color='#551A8B')

#Limites de espa�amento do ponto (0,0)
plt.xlim([min(datax) - 1.0, max(datax) + 1.0])
plt.ylim([min(datay) - 30, max(datay) + 10])

#Masked arrays are also supported
#masked_u = np.ma.masked_array(datau)
#masked_u[4] = 1000 #Bad value that should not be plotted when masked
#masked_u[4] = np.ma.masked

plt.xticks(x,xticks,rotation=30)
plt.yticks(y,yticks, rotation=30)
plt.title(u'Hist�rico do Perfil de Vento')
#plt.rcParams['xtick.major.pad']='50'

plt.xlabel(u"Hora (UTC)")
plt.ylabel(u"Altura (p�s)")

#plt.savefig('grafico2.png')
#plt.imshow(X)
#plt.colorbar()
plt.show()

#ax.plot([1,2,3])
fig.set_size_inches(11.8,8.5)
fig.savefig(diretorio_graficos+'vetor24h.png', bbox_inches='tight')
