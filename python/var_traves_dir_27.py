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

cur.execute("SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT MAX(d.sodar_atual_data) FROM dados_derivados d)")

for row in cur.fetchall() :
    y1.append(row[7])
    x1.append(round(row[5],0))

cur.execute("SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT DATE_SUB(MAX(d.sodar_atual_data), INTERVAL '15' MINUTE) FROM  dados_derivados d)")

for row in cur.fetchall() :
    y2.append(row[7])
    x2.append(round(row[5],0))

fig = plt.figure()
ax = plt.subplot(1,1,1)

plt.title(u'Varia��o de trav�s_dir')

windshear1 = ax.plot(x1, y1, label="Atual")  # plot x and y using blue circle markers
windshear2 = ax.plot(x2, y2, label="Anterior")  # plot x and y using blue circle markers

plt.legend(prop={'size':10})

##fig.legend((windshear1,windshear2),('Recente','Antiga'),'best')

xticks = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]
yticks = ('', 300, 500, '', 900, 1100, 1300, 1500, 1700)
x = np.linspace(0, 30,31)
y = np.linspace(30, 510,9)
plt.xticks(x,xticks,rotation=70)
plt.yticks(y,yticks,rotation='horizontal')

ax.xaxis.grid(color='#B0C4DE', linestyle='dashed', zorder=-6)

plt.xlabel(u"Velocidade (kt)")
plt.ylabel(u"Altura (p�s)")

plt.axhline(altitude1*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude2*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude3*0.305, color='#551A8B', zorder=-3, linestyle='dashed')
plt.axhline(altitude4*0.305, color='#551A8B', zorder=-3, linestyle='dashed')

ax.text(-1.5, altitude1*0.305, nome_altitude1, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.5, altitude2*0.305 , nome_altitude2, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.5, altitude3*0.305 , nome_altitude3, rotation=30, fontsize=9, color='#551A8B')
ax.text(-1.5, altitude4*0.305 , nome_altitude4, rotation=30, fontsize=9, color='#551A8B')

fig.set_size_inches(6.8,6.5)
fig.savefig(diretorio_graficos+'var_traves_dir_27.png', bbox_inches='tight')
