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

db = MySQLdb.connect(host=host, user=user, passwd=password, db=database)

cur = db.cursor() 
cur.execute("SELECT s.data, s.z, s.speed, s.dir, s.U, s.V from sodar_atual s where s.data > DATE_SUB(NOW(), INTERVAL 45 MINUTE)AND s.data < NOW() AND (s.z=30 OR s.z=100 OR s.z=200 OR s.z=300) ORDER BY s.data DESC, s.z DESC")

cur2 = db.cursor() 
cur2.execute("SELECT datahora FROM processamento;")
row = cur2.fetchone()
datahora =str(row[0])

cur3 = db.cursor() 
##cur3.execute("(SELECT vel15_300, vel15_200, vel15_100, vel15_sup, dir15_300, dir15_200, dir15_100, dir15_SUP FROM saida_RNA s2 WHERE s2.datahora=%s) UNION (SELECT vel30_300, vel30_200, vel30_100, vel30_sup, dir30_300, dir30_200, dir30_100, dir30_SUP FROM saida_RNA s2 WHERE s2.datahora=%s)UNION(SELECT vel45_300, vel45_200, vel45_100, vel45_sup, dir45_300, dir45_200, dir45_100, dir45_SUP FROM saida_RNA s2 WHERE s2.datahora=%s);", (datahora,datahora,datahora))
cur3.execute("SELECT * FROM saida_RNA WHERE datahora=%s",(datahora))

# altitude1 = 30
# altitude2 = 130
# altitude3 = 230
# altitude4 = 330
# nome_altitude1 = 'SUP'
# nome_altitude2 = '330'
# nome_altitude3 = '660'
# nome_altitude4 = '1000'
	
datau = [] #Componente U
datav = [] #Componente V
datax = [] #Coordenada X
datay = [] #Coordenada Y
datas = [] #Vetor de velocidades
xticks = [] #Vetor de Legendas para o eixo X
cor = [] #Vetor de cores das barbelas

##datax = (1,2,3,4,5,6,7,8,9,10)
##datay = (1,2,3,4,5,6,7,8,9,10)

x = 6 ## Número de observações com intervalo de 15 minutos para 3 horas 
a = 4 ## Número de alturas , indo de SUP a 100 temos 4 alturas
y = 6 ## Numero de observacoes com intervalo de 15 minutos para 3 horas 
z = 340 ## Altura máxima acrescida de 10 unidades


hora_obs_atual = []

for hora_obs in range(0,x-3,1):
    hora_atual_grafico = datetime.datetime.now() - datetime.timedelta(seconds=hora_obs*900)
    if (hora_atual_grafico.minute >=00 and hora_atual_grafico.minute <15):
        minutos_atual = '00'
    if (hora_atual_grafico.minute >=15 and hora_atual_grafico.minute <30):
        minutos_atual = '15'
    if (hora_atual_grafico.minute >=30 and hora_atual_grafico.minute <45):
        minutos_atual = '30'
    if (hora_atual_grafico.minute >=45 and hora_atual_grafico.minute <60):
        minutos_atual = '45'
    for n in range(0,4,1):
        hora_obs_atual.append(hora_atual_grafico.strftime('%Y-%m-%d %H:') + minutos_atual + ':00')


for i in range(0,x,1):
    for l in range(a):
        datax.append(i)
    
for j in range(y):
    for k in range(30,z,100):
        datay.append(k)


for row3 in cur3.fetchall() :
	################45 minutos################ ATENCAO PARA NUMEROS NEGATIVOS DE U E V
	#altitude 300m
	datau.append(np.sin(np.deg2rad(row3[15])-70)*row3[18]*(-1))
	datav.append(np.cos(np.deg2rad(row3[15])-70)*row3[18]*(-1))
	datas.append(row3[18])
	#altitude 200m
	datau.append(np.sin(np.deg2rad(row3[14])-70)*row3[17]*(-1))
	datav.append(np.cos(np.deg2rad(row3[14])-70)*row3[17]*(-1))
	datas.append(row3[17])
	#altitude 100m
	datau.append(np.sin(np.deg2rad(row3[13])-70)*row3[16]*(-1))
	datav.append(np.cos(np.deg2rad(row3[13])-70)*row3[16]*(-1))
	datas.append(row3[16])
	#superficie
	datau.append(np.sin(np.deg2rad(row3[21])-70)*row3[24]*(-1)*(0.5144))
	datav.append(np.cos(np.deg2rad(row3[21])-70)*row3[24]*(-1)*(0.5144))
	datas.append(row3[24]*(0.5144))

	################30 minutos ###################
	#altitude 300m
	datau.append(np.sin(np.deg2rad(row3[9])-70)*row3[12]*(-1))
	datav.append(np.cos(np.deg2rad(row3[9])-70)*row3[12]*(-1))
	datas.append(row3[12])
	#altitude 200m
	datau.append(np.sin(np.deg2rad(row3[8])-70)*row3[11]*(-1))
	datav.append(np.cos(np.deg2rad(row3[8])-70)*row3[11]*(-1))
	datas.append(row3[11])
	#altitude 100m
	datau.append(np.sin(np.deg2rad(row3[7])-70)*row3[10]*(-1))
	datav.append(np.cos(np.deg2rad(row3[7])-70)*row3[10]*(-1))
	datas.append(row3[10])
	#superficie
	datau.append(np.sin(np.deg2rad(row3[20])-70)*row3[23]*(-1)*(0.5144))
	datav.append(np.cos(np.deg2rad(row3[20])-70)*row3[23]*(-1)*(0.5144))
	datas.append(row3[23]*(0.5144))

	####################15 minutos	###################
	#altitude 300m
	datau.append(np.sin(np.deg2rad(row3[3])-70)*row3[6]*(-1))
	datav.append(np.cos(np.deg2rad(row3[3])-70)*row3[6]*(-1))
	datas.append(row3[6])
	#altitude 200m
	datau.append(np.sin(np.deg2rad(row3[2])-70)*row3[5]*(-1))
	datav.append(np.cos(np.deg2rad(row3[2])-70)*row3[5]*(-1))
	datas.append(row3[5])
	#altitude 100m
	datau.append(np.sin(np.deg2rad(row3[1])-70)*row3[4]*(-1))
	datav.append(np.cos(np.deg2rad(row3[1])-70)*row3[4]*(-1))
	datas.append(row3[4])
	#superficie
	datau.append(np.sin(np.deg2rad(row3[19])-70)*row3[22]*(-1)*(0.5144))
	datav.append(np.cos(np.deg2rad(row3[19])-70)*row3[22]*(-1)*(0.5144))
	datas.append(row3[22]*(0.5144))

print len(datas)
contador = 0

 ## print all the first cell of all the rows
for row in cur.fetchall() :
   if contador >len(hora_obs_atual):
             break
   if str(row[0])== str(hora_obs_atual[contador]):
        if (row[4]== 99.99):
            datau.append(0) 
        else:
            datau.append(row[4]*(-1))
        if (row[5]== 99.99):
             datav.append(0) 
        else:
             datav.append(row[5]*(-1))
        ##Adicionando lista velocidade    
        datas.append(row[2])
        contador +=1
	
        
   else:
         while str(row[0]) != str(hora_obs_atual[contador]):
             datau.append(0)
             datav.append(0)
             contador +=1
             if contador >len(hora_obs_atual):
                break
         if contador >len(hora_obs_atual):
             break
         if (row[4]== 99.99):
             datau.append(0) 
         else:
             datau.append(row[4]*(-1))
         if (row[5]== 99.99):
             datav.append(0) 
         else:
             datav.append(row[5]*(-1))
         ##Adicionando lista velocidade    
         datas.append(row[2])
         contador +=1


print len(datas)

# hora_atual = datetime.datetime.now() 
# if (hora_atual.minute >=00 and hora_atual.minute <15):
#     minutos = '00'
# if (hora_atual.minute >=15 and hora_atual.minute <30):
#     minutos = '15'
# if (hora_atual.minute >=30 and hora_atual.minute <45):
#     minutos = '30'
# if (hora_atual.minute >=45 and hora_atual.minute <60):
#     minutos = '45'
    
for hora in range(0,6,1):
    hora_atual = datetime.datetime.now() + datetime.timedelta(seconds=2700) - datetime.timedelta(seconds=hora*900)
    #if (hora==0):
    if (hora_atual.minute >=00 and hora_atual.minute <15):
     minutos = '00'
    if (hora_atual.minute >=15 and hora_atual.minute <30):
     minutos = '15'
    if (hora_atual.minute >=30 and hora_atual.minute <45):
     minutos = '30'
    if (hora_atual.minute >=45 and hora_atual.minute <60):
     minutos = '45'
    #else:
        #if (hora_atual.minute >=00 and hora_atual.minute <15):
        #    minutos = '15'
        #if (hora_atual.minute >=15 and hora_atual.minute <30):
        #    minutos = '30'
        #if (hora_atual.minute >=30 and hora_atual.minute <45):
        #    minutos = '45'
        #if (hora_atual.minute >=45 and hora_atual.minute <60):
        #    minutos = '00'
    horario = str(hora_atual.hour) + ":" + minutos
    xticks.append(horario)

#print xticks

##Adicionando label
##for row2 in cur2.fetchall() :
    ##xticks.append(row2[0])


## Invertendo todos os vetores
xticks.reverse()
datau.reverse()
datav.reverse()
datas.reverse()
   
for speed in range(len(datas)) :
    if (datas[speed]== 99.99):
        ##cor.append('#FFDAB9')
        abc = 2+3
    elif ((datas[speed]>0) and (datas[speed]<1.25)):
        cor.append('#000000')
    elif ((datas[speed]>=1.25) and (datas[speed]<2)):
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

        
#print datau
#print datav
#print datax
#print datay
print datas
#print xticks
#print masked_u


#---------------------------- Pode ser útil no futuro ----------------------------------------

#ax.xaxis.set_ticks([1.,2.,3.,10.])



#---------------------------------------------------------------------------------------------
# (coordenada de x para a primeira legenda, coordenada de x para a última legenda, numero de legendas de x)
x = np.linspace(0, 5, 6)
y = np.linspace(30,330, 4)

yticks = ('SUP', 330, 660, 1000)

X,Y = np.meshgrid(x, x)
U, V = 5*X, 5*Y

#fig = plt.imshow(100)
#ax2 = fig.add_subplot(111)
#mn=5       # colorbar min value
#mx=15         # colorbar max value
#md=50                  # colorbar midpoint value
#cbar=plt.colorbar()              # the mystery step ???????????
#cbar.set_ticks([mn,md,mx])
#cbar.set_ticklabels([mn,md,mx])

##datau = np.ma.masked_equal(datau, 99.99)
##print datau
#datav = np.ma.masked_equal(datav, 99.99)
#print datav

#Change colors as well as the increments for parts of the barbs
fig = plt.figure()
ax = plt.subplot(1,1,1)

barbela = ax.barbs(datax, datay, masked_u, datav, flagcolor='r', length=7, 
    barbcolor=cor, barb_increments=dict(half=2.5, full=5, flag=24),
   flip_barb=True,pivot='middle', sizes=dict(emptybarb=0.1, spacing=0.2, height=0.2))


#vetor = ax.quiver(datau, datav, cor)
#qk = ax.quiverkey(vetor,datax,datay, 7, 'vento')
ax.yaxis.grid(color='#B0C4DE', linestyle='dashed', zorder=-6)
ax.xaxis.grid(color='#B0C4DE', linestyle='dashed', zorder=-6)

plt.axvline(2.5, color='#551A8B', zorder=-3, linestyle='dashed')

# plt.axhline(altitude1*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
# plt.axhline(altitude2*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
# plt.axhline(altitude3*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
# plt.axhline(altitude4*0.305, color='#551A8B', zorder=-3, linestyle='dashed')

# ax.text(-1.5, altitude1*0.305, nome_altitude1, rotation=30, fontsize=9, color='#551A8B')
# ax.text(-1.5, altitude2*0.305 , nome_altitude2, rotation=30, fontsize=9, color='#551A8B')
# ax.text(-1.5, altitude3*0.305 , nome_altitude3, rotation=30, fontsize=9, color='#551A8B')
# ax.text(-1.5, altitude4*0.305 , nome_altitude4, rotation=30, fontsize=9, color='#551A8B')

##plt.axhline(y=yline, linestyle='-', color='white', linewidth=4, zorder=3)

#Limites de espaçamento do ponto (0,0)
plt.xlim([min(datax) - 1.0, max(datax) + 1.0])
plt.ylim([min(datay) - 30, max(datay) + 30])

#Masked arrays are also supported
#masked_u = np.ma.masked_array(datau)
#masked_u[4] = 1000 #Bad value that should not be plotted when masked
#masked_u[4] = np.ma.masked

plt.xticks(x,xticks,rotation='horizontal')
plt.yticks(y,yticks, rotation=30)
#plt.title(u'Histórico do Perfil de Vento')

plt.xlabel(u"Hora (hh:mm)")
plt.ylabel(u"Altura (ft)")

#plt.rcParams['xtick.major.pad']='50'

#plt.savefig('grafico2.png')
#plt.imshow(X)
#plt.colorbar()
plt.show()

#ax.plot([1,2,3])
fig.set_size_inches(6.7,6.0)
fig.savefig(diretorio_graficos+'barbela.png', bbox_inches='tight')



"""(SELECT s.data, s.speed, s.dir, s.U, s.V from sodar_atual s where s.data > DATE_SUB(NOW(), INTERVAL 45 MINUTE) 
AND s.data < NOW() AND (s.z=100 OR s.z=200 OR s.z=300) )

(SELECT vel15_300, vel15_200, vel15_100, vel15_sup, dir15_300, dir15_200, dir15_100, dir15_SUP FROM saida_RNA s2 WHERE s2.data) 
UNION
(SELECT vel30_300, vel30_200, vel30_100, vel30_sup, dir30_300, dir30_200, dir30_100, dir30_SUP FROM saida_RNA s2 WHERE s2.data)
UNION
(SELECT vel15_300, vel15_200, vel15_100, vel15_sup, dir15_300, dir15_200, dir15_100, dir15_SUP FROM saida_RNA s2 WHERE s2.data)"""
