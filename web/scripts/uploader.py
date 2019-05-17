import mysql.connector as mariadb
import os

SQL_DIR = '/sql'
HOST    = '10.128.0.4' 
USER    = 'root'
PASS    = 'qwerty12345ytrewq'
DB      = 'users_db'
PORT    = 3306

is_error      = False
is_db_present = False

def getConnection(hst,usr,pwd, db, prt):
   try:
     if len(db)==0:
        return mariadb.connect(host=hst, user=usr, password=pwd, port=prt)
     else:
        return mariadb.connect(host=hst, user=usr, password=pwd, database=db, port=prt)
   except:
     return None

def dropDatabase(conn, dbname):
   try:
     sql = 'DROP DATABASE {}'.format(dbname) 
     cursor = conn.cursor()
     cursor.execute(sql)
     cursor.close()
     return True
   except:
     return False  


def createDatabase(conn, dbname):
   try:
     sql = 'CREATE DATABASE {}'.format(dbname) 
     cursor = conn.cursor()
     cursor.execute(sql)
     cursor.close() 
     return True
   except:
     return False 


def isDbPresent(conn, dbname):
   isPresent = False
   cursor = conn.cursor()

   try:
     sql = 'SHOW DATABASES' 
     cursor.execute(sql)
     for txt in cursor:
        if str(txt).find(dbname) >= 0:
           isPresent = True  
           break
   except:
     pass

   return isPresent 

def uploadData(conn, txt):
   success = False
   cursor = conn.cursor()
   try:
     cursor.execute(txt)
     success = True
   except Exception as ex:
     pass

   return success
     


mc = getConnection(HOST,USER,PASS,'',PORT)

if not type(mc) is None:
   dropDatabase(mc, DB)
   createDatabase(mc, DB)
   is_db_present =isDbPresent(mc, DB)
else:
   is_error = True
mc.close()

if os.path.exists(SQL_DIR) and not is_error and is_db_present:
   files = os.listdir(SQL_DIR+'/')
   if len(files)>0:
      
      for fl in files:
         sql=''
         with open(SQL_DIR+'/'+fl) as file:
            for line in file:
               sql = sql +line
      mc = getConnection(HOST,USER,PASS,DB,PORT)
      uploadData(mc, sql)
      mc.close()     
