#!/usr/bin/env python 1
# -*- coding: utf-8 -*-

import paho.mqtt.client as mqtt
import sys
import MySQLdb

# Conectar con la bases de datos
try:
    db = MySQLdb.connect("127.0.0.1","root","ed318","proyecto_final")
    print("Conectado a la base de datos")
except:
    print("No se pudo conectar con la base de datos")
    print("Cerrando...")
    sys.exit()

# Cursor
cursor = db.cursor()

# callback cuando el cliente recibe una respuesta del broker.
def on_connect(client, userdata, flags, rc):
    print("Conectado al broker - Codigo de resultado: "+str(rc))
    #Suscripcion a todos los topics.
    client.subscribe("/#")

# callback cuando se recibe un mensaje de publicacion desde el servidor.
def on_message(client, userdata, msg):
    print(msg.topic+" "+str(msg.payload))
    lista = msg.topic.split("/")
    
    sql = """INSERT INTO `proyecto_final`.`datos` ( `usuario`, `topic`, `payload`, `fecha`) VALUES ('""" + lista[2]+ """', '""" + lista[3] + """', '""" + str(msg.payload) + """', CURRENT_TIMESTAMP);"""
    
    try:
        # Ejecutar comando SQL
        cursor.execute(sql)
        db.commit()
        print("Guardando en base de datos...OK")
    except:
        db.rollback()
        print("Guardando en base de datos...Falló")
        
client = mqtt.Client()
client.username_pw_set("mqtt_mysql","ed318")
client.on_connect = on_connect
client.on_message = on_message


try:
    client.connect("127.0.0.1", 1883, 60)
except:
    print("No se pudo conectar con el MQTT Broker...")
    print("Cerrando...")
    db.close()
    sys.exit()   

#loop. el cliente queda conectado hasta que se produzca una interrupcion por teclado.
#Este bucle maneja automaticamente la reconexion con los parametros de la primera conexion.
try:
    client.loop_forever()
except KeyboardInterrupt:  #presionar Crtl + C para salir
    print("Cerrando...")
    db.close()
