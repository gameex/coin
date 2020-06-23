# -*- coding: utf-8 -*-
import time
import sys
import MySQLdb
import requests
import json
import os
import ConfigParser

def Check(cur,conn):
        count = cur.execute("select id,order_time from jl_otc_order where status = 2 and unix_timestamp(order_time) < %d" % (int(time.time()) - 1800))
        print '%s have %d otc order time out' % (time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()),count)
        if count == 0:
                return
        result = cur.fetchall()
        ids = [k[0] for k in result]
        idss = (','.join('%s' % id for id in ids))
        update_sql = "update jl_otc_order set status = 0 where id in (%s)" % idss
        print update_sql
        cur.execute(update_sql)
        conn.commit()

def Run():
        conn= Conn()
        cur=conn.cursor()
        Check(cur,conn)
        cur.close()
        conn.close()

def Conn():
        cp = ConfigParser.SafeConfigParser()
        the_dir = sys.path[0]
        print the_dir
        cp.read(the_dir+'/db.conf')
        return MySQLdb.connect(host=cp.get('db', 'host'),user=cp.get('db', 'user'),passwd=cp.get('db', 'pass'),db=cp.get('db', 'name'),port=int(cp.get('db', 'port')),charset=cp.get('db', 'charset'))
        #return MySQLdb.connect(host='127.0.0.1',user='root',passwd='root',db='kkwallet',port=3306, charset="utf8")

  
if __name__ == '__main__':
        while True:
                try:
                        print '================Run start=============='
                        Run()
                        print '================Run end=============='
                        time.sleep(10)
                except MySQLdb.Error,e:
                        print "Mysql Error %d: %s" % (e.args[0], e.args[1])
                        time.sleep(20)
