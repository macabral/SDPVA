# -*- coding: cp1252 -*-
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
x1 = []
y1 = []
x2 = []
y2 = []
x3 = []
y3 = []
x4 = []
y4 = []
x5 = []
y5 = []
x6 = []
y6 = []
hora = []

cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT MAX(s2.data) FROM sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y1.append(row[1])
x1.append(abs(row[2]))

for row in cur.fetchall() :
    y1.append(row[1])
    x1.append(abs(row[2]))

cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT DATE_SUB(MAX(s2.data), INTERVAL '15' MINUTE) FROM  sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y2.append(row[1])
x2.append(abs(row[2]))

for row in cur.fetchall() :
    y2.append(row[1])
    x2.append(abs(row[2]))

cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT DATE_SUB(MAX(s2.data), INTERVAL '30' MINUTE) FROM  sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y3.append(row[1])
x3.append(abs(row[2]))

for row in cur.fetchall() :
    y3.append(row[1])
    x3.append(abs(row[2]))
    
cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT DATE_SUB(MAX(s2.data), INTERVAL '45' MINUTE) FROM  sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y4.append(row[1])
x4.append(abs(row[2]))

for row in cur.fetchall() :
    y4.append(row[1])
    x4.append(abs(row[2]))
    
cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT DATE_SUB(MAX(s2.data), INTERVAL '60' MINUTE) FROM  sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y5.append(row[1])
x5.append(abs(row[2]))

for row in cur.fetchall() :
    y5.append(row[1])
    x5.append(abs(row[2]))
    
cur.execute("SELECT TIME_FORMAT(s.data,'%H:%i') as hora, s.z, ROUND(s.shear*59.15, 2) as shear FROM sodar_atual s  WHERE s.data=(SELECT DATE_SUB(MAX(s2.data), INTERVAL '75' MINUTE) FROM  sodar_atual s2) AND s.shear<99.999")

row = cur.fetchone()
hora.append(row[0])
y6.append(row[1])
x6.append(abs(row[2]))

for row in cur.fetchall() :
    y6.append(row[1])
    x6.append(abs(row[2]))

print hora
print x1
print x6
    
fig = plt.figure()
ax = plt.subplot(1,1,1)

ax.yaxis.grid(color='#B0C4DE', linestyle='dashed', zorder=-6)
ax.xaxis.grid(color='#B0C4DE', linestyle='dashed', zorder=-6)

windshear6 = ax.plot(x6, y6,label=hora[5], color='blue')  # plot x and y using blue circle markers
windshear5 = ax.plot(x5, y5,label=hora[4], color='green')  # plot x and y using blue circle markers
windshear4 = ax.plot(x4, y4,label=hora[3], color='#FFD700')  # plot x and y using blue circle markers
windshear3 = ax.plot(x3, y3,label=hora[2], color='orange')  # plot x and y using blue circle markers
windshear2 = ax.plot(x2, y2,label=hora[1], color='black', linewidth='2.0')  # plot x and y using blue circle markers
windshear1 = ax.plot(x1, y1,label=hora[0], color='red', linewidth='2.0')  # plot x and y using blue circle markers

plt.legend(prop={'size':12})

plt.title('Windshear')

plt.xlabel(u"Windshear (kt/100 ft)")
plt.ylabel(u"Altura (ft)")

## Destacando altitudes críticas
plt.axhline(altitude1*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude2*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude3*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude4*0.305, color='#551A8B', zorder=-3, linestyle='dashed')

ax.text(-1.0, altitude1*0.305, nome_altitude1, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.0, altitude2*0.305 , nome_altitude2, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.0, altitude3*0.305 , nome_altitude3, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.0, altitude4*0.305 , nome_altitude4, rotation=30, fontsize=9, color='#551A8B')

## Destacando valores limite de windshear 
plt.axvline(4, linestyle='dashed', color='black', zorder=3)
plt.axvline(8, linestyle='dashed', color='black', zorder=3)
plt.axvline(12, linestyle='dashed', color='black', zorder=3)

xticks = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
yticks = ['',300,500,'']
x = np.linspace(0, 15, 16)
y = np.linspace(30, 220, 4)
plt.xticks(x,xticks,rotation='horizontal')
plt.yticks(y,yticks,rotation='horizontal')

fig.set_size_inches(6.8,6.5)
fig.savefig(diretorio_graficos+'windshear6x1.png', bbox_inches='tight')
