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
diretorio_xml = ''.join(config_lines[11].split('\r\n'))

##var = raw_input("Please enter something: ")
##print "you entered", var

db = MySQLdb.connect(host=host, user=user, passwd=password, db=database)

cur = db.cursor()
cur2 = db.cursor()

nome= ''

##cur.execute("SELECT s.id, s.data, s.z, s.speed, s.dir, s.U, s.V from sodar_ncqar s where s.data > DATE_SUB(NOW(), INTERVAL 1455 MINUTE) AND s.data < NOW() AND s.z <=520 order by s.id ASC, s.data DESC, s.z DESC")
cur.execute("SELECT MAX(d2.sodar_atual_data) FROM dados_derivados d2")
cur2.execute("SELECT s.id, s.data as data_atual, s.z, s.speed, s.dir, s.U, s.V, s.shear, td.proa, td.cauda, td.traves_esq, td.traves_dir FROM sodar_atual s INNER JOIN (SELECT d.proa, d.cauda, d.traves_esq, d.traves_dir, sodar_atual_z FROM dados_derivados d WHERE d.sodar_atual_data=(SELECT MAX(d2.sodar_atual_data) FROM dados_derivados d2)) td ON s.z=td.sodar_atual_z WHERE s.data =(SELECT MAX(s2.data) FROM sodar_atual s2) AND s.data < NOW() AND s.z <=520 AND s.speed<99.99")

nome_data = cur.fetchone()[0] ##data do root do xml

##Criando árvore XML e adicionando os valores
root=ET.Element("sdpva", local="SBGR", datahora=nome_data.strftime("%Y-%m-%dT%H:%M:%S"), tipo="D", pista="27")
for row in cur2.fetchall():
    head2=ET.SubElement(root,"Obs")

    title=ET.SubElement(head2,"datahora")
    title.text=(str(row[1]))

    title=ET.SubElement(head2,"altura")
    title.text=(str(row[2]))

    title=ET.SubElement(head2,"velocidade")
    title.text=(str(row[3]))

    title=ET.SubElement(head2,"direcao")
    title.text=(str(row[4]))

    title=ET.SubElement(head2,"U")
    title.text=(str(row[5]))

    title=ET.SubElement(head2,"V")
    title.text=(str(row[6]))

    title=ET.SubElement(head2,"windshear")
    title.text=(str(row[7]))

    title=ET.SubElement(head2,"proa")
    title.text=(str(row[9]))

    title=ET.SubElement(head2,"cauda")
    title.text=(str(row[8]))

    title=ET.SubElement(head2,"traves_esq")
    title.text=(str(row[11]))

    title=ET.SubElement(head2,"traves_dir")
    title.text=(str(row[10]))

    nome = str(row[1])
    
nome = nome.replace(" ", "") ##Removendo espaços em branco
nome = nome.replace(":", "") ##Removendo :
nome = nome.replace("-", "") ##Removendo traços
nome = nome[:-2] ## Removendo os segundos
nome = nome + ".xml" ##Adicionando extensão do arquivo

tree=ET.ElementTree(root)
tree.write(diretorio_xml + '27/' + nome)##Salvando o arquivo
