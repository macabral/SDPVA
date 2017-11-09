# -*- coding: cp1252 -*-
##import elementtree.ElementTree as ET
from lxml import etree as ET
import MySQLdb

f=open('/var/www/html/SDPVA/application/configs/config_python.txt')
config_lines=f.readlines()
host = ''.join(config_lines[1].split('\r\n'))
user = ''.join(config_lines[3].split('\r\n'))
password = ''.join(config_lines[5].split('\r\n'))
database = ''.join(config_lines[7].split('\r\n'))
diretorio_xml = ''.join(config_lines[13].split('\r\n'))

##var = raw_input("Please enter something: ")
##print "you entered", var

db = MySQLdb.connect(host=host, user=user, passwd=password, db=database)

cur = db.cursor()
cur2 = db.cursor()
#cur3 = db.cursor()

nome= ''

##cur.execute("SELECT s.id, s.data, s.z, s.speed, s.dir, s.U, s.V from sodar_atual s where s.data > DATE_SUB(NOW(), INTERVAL 1455 MINUTE) AND s.data < NOW() AND s.z <=520 order by s.id ASC, s.data DESC, s.z DESC")
cur.execute("SELECT datahora FROM processamento ")
nome_data = cur.fetchone()[0] ##data do root do xml
datahora = nome_data.strftime("%Y-%m-%d %H:%M:%S")

cur2.execute("""SELECT * FROM sodar_atual s WHERE s.data=%s """, (datahora))

##Criando �rvore XML e adicionando os valores
root=ET.Element("sdpva", local="SBGR", datahora=nome_data.strftime("%Y-%m-%dT%H:%M:%S"), tipo="G")
for row in cur2.fetchall():
    head2=ET.SubElement(root,"Obs")

    title=ET.SubElement(head2,"z")
    title.text=(str(row[2]))

    title=ET.SubElement(head2,"speed")
    title.text=(str(row[3]))

    title=ET.SubElement(head2,"dir")
    title.text=(str(row[4]))

    title=ET.SubElement(head2,"U")
    title.text=(str(row[5]))

    title=ET.SubElement(head2,"V")
    title.text=(str(row[6]))

    title=ET.SubElement(head2,"W")
    title.text=(str(row[7]))

    title=ET.SubElement(head2,"sigU")
    title.text=(str(row[8]))

    title=ET.SubElement(head2,"sigU_r")
    title.text=(str(row[9]))

    title=ET.SubElement(head2,"sigV")
    title.text=(str(row[10]))

    title=ET.SubElement(head2,"sigV_r")
    title.text=(str(row[11]))

    title=ET.SubElement(head2,"sigW")
    title.text=(str(row[12]))

    title=ET.SubElement(head2,"speed_ass")
    title.text=(str(row[13]))

    title=ET.SubElement(head2,"dir_ass")
    title.text=(str(row[14]))

    title=ET.SubElement(head2,"U_ass")
    title.text=(str(row[15]))

    title=ET.SubElement(head2,"V_ass")
    title.text=(str(row[16]))

    title=ET.SubElement(head2,"W_ass")
    title.text=(str(row[17]))

    title=ET.SubElement(head2,"sigU_ass")
    title.text=(str(row[18]))

    title=ET.SubElement(head2,"sigU_r_ass")
    title.text=(str(row[19]))

    title=ET.SubElement(head2,"sigV_ass")
    title.text=(str(row[20]))

    title=ET.SubElement(head2,"sigV_r_ass")
    title.text=(str(row[21]))

    title=ET.SubElement(head2,"sigW_ass")
    title.text=(str(row[22]))

    title=ET.SubElement(head2,"shear")
    title.text=(str(row[23]))

    title=ET.SubElement(head2,"shearDir")
    title.text=(str(row[24]))

    title=ET.SubElement(head2,"sigSpeed")
    title.text=(str(row[25]))

    title=ET.SubElement(head2,"sigLat")
    title.text=(str(row[26]))

    title=ET.SubElement(head2,"sigPhi")
    title.text=(str(row[27]))

    title=ET.SubElement(head2,"sigTheta")
    title.text=(str(row[28]))

    title=ET.SubElement(head2,"TI")
    title.text=(str(row[29]))

    title=ET.SubElement(head2,"PGz")
    title.text=(str(row[30]))

    title=ET.SubElement(head2,"TKE")
    title.text=(str(row[31]))

    title=ET.SubElement(head2,"EDR")
    title.text=(str(row[32]))

    title=ET.SubElement(head2,"bck_raw")
    title.text=(str(row[33]))

    title=ET.SubElement(head2,"bck")
    title.text=(str(row[34]))

    title=ET.SubElement(head2,"bck_ID")
    title.text=(str(row[35]))

    title=ET.SubElement(head2,"CT2")
    title.text=(str(row[36]))

    title=ET.SubElement(head2,"error")
    title.text=(str(row[37]))

    title=ET.SubElement(head2,"PG")
    title.text=(str(row[38]))

    title=ET.SubElement(head2,"h_range")
    title.text=(str(row[39]))

    title=ET.SubElement(head2,"h_inv")
    title.text=(str(row[40]))

    title=ET.SubElement(head2,"h_mixing")
    title.text=(str(row[41]))

    title=ET.SubElement(head2,"H")
    title.text=(str(row[42]))

    title=ET.SubElement(head2,"u_estrela")
    title.text=(str(row[43]))

    title=ET.SubElement(head2,"L_estrela")
    title.text=(str(row[44]))

    nome = str(row[1])
    
nome = nome.replace(" ", "") ##Removendo espa�os em branco
nome = nome.replace(":", "") ##Removendo :
nome = nome.replace("-", "") ##Removendo tra�os
nome = nome[:-2] ## Removendo os segundos
nome = nome + ".xml" ##Adicionando extens�o do arquivo

tree=ET.ElementTree(root)

##tree.str.replace('datahora=""','datahora="'+nome_data+'"')
tree.write(diretorio_xml + nome)##Salvando o arquivo

################## FAZER O UPDATE AQUI ####################
##data_execucao = nome_data.strftime("%Y-%m-%d %H:%M:%S")
##cur3.execute("""UPDATE processamento SET datahora=%s , nomearqxml_d=%s, flag_RNA=1""",(data_execucao,nome))
##db.commit()




