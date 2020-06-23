#!/usr/bin/python
#coding=UTF-8
import sys
import urllib2
import urllib
import random
import math
import time
import os
import requests
import ConfigParser
import subprocess
import MySQLdb
import json
#reload(sys)
#sys.setdefaultencoding('utf-8')

def Conn():
    cp = ConfigParser.SafeConfigParser()
    the_dir = sys.path[0]
    print the_dir
    cp.read(the_dir+'/db.conf')
    return MySQLdb.connect(host=cp.get('db', 'host'),user=cp.get('db', 'user'),passwd=cp.get('db', 'pass'),db=cp.get('db', 'name'),port=int(cp.get('db', 'port')),charset=cp.get('db', 'charset'))
    #return MySQLdb.connect(host='127.0.0.1',user='root',passwd='root',db='kkwallet',port=3306, charset="utf8")

def update_huobi():
    host = 'http://huibi.xyz/api/robot/robot-list'
    try:
        response = requests.get(host)
        print(response.text)  #以文本形式打印网页源码
    except requests.RequestException as e:
        print e

def rpc_query(params):
    host = 'http://47.52.167.211:8080'
    headers = {'Content-Type': 'application/json', 'Connection':'close'}
    post_data = {"id":22,"method":"order.depth","params":params}
    encode_json = json.dumps(post_data)
    #print encode_json
    try:
        P_post=requests.post(host,headers=headers,data=encode_json,timeout=5)
        if P_post.status_code == 200:
            rst = json.loads(P_post.text)
            #print rst
            if rst.has_key('result'):
                return rst['result']
            else:
                return None
        else:
            return None
    except requests.RequestException as e:
        print e
        return None   

def get_num(market,type,price):
    param =  [market,50,'0.1']
    data =  rpc_query(param)
    print data

    if data is None:
        return 0    
    data_ask = data['asks']
    data_bid = data['bids']             
    #print data_ask
    #print data_bid
    price = float(price)
    total_num = 0
    if(type==2):
        for item in data_ask:
            p = float(item[0])
            num =  float(item[1])
            #print p
            #print price
            if(p<price):
                total_num = total_num + num
                print '###'+  str(num)
    else:
         for item in data_bid:
            p = float(item[0])
            num =  float(item[1])
            if(p>price):
                total_num = total_num + num    
                print '###'+  str(num)
    print total_num
    return float(total_num)

def lastUpdateTime(cur):
    assets_count = cur.execute("select symbol from jl_coins where enable = 1 order by listorder desc")
    #print 'db have %d coins' % assets_count
    assets = [k[0] for k in cur.fetchall()]
    #print assets

    assetss = (','.join("'%s'" % k for k in assets))

    markets_count = cur.execute("select stock,money,id from jl_exchange_coins where enable = 1 and stock in (%s) and money in (%s) order by listorder desc" % (assetss,assetss))
    print 'db have %d market' % markets_count
    old_markets = cur.fetchall()
    marketinfo = {k[2]:k[0]+k[1] for k in old_markets}
    #print marketinfo

    marketids = (','.join('%d' % k for k in [j[2] for j in old_markets]))
    print marketids
    
    cur.execute('SELECT b.access_token,a.* FROM `jl_robot` a INNER JOIN `jl_api_access_token` b ON a.uid = b.`user_id` where a.market_id in (%s) and a.status = 1 LIMIT 60' % (marketids))
    data = cur.fetchall()
    print data
    if data:
        for info in data:
            print '-------------' 
            market_name = marketinfo[int(info[3])]
            print 'market_name:' + str(market_name)

            paytype = random.randint(1, 2) #2买入 1卖出
            print 'type:' + str(paytype)
            price = random.uniform(float(info[4]), float(info[5]))

            total_num = get_num(market_name,paytype,price)
            print 'total_num:'+str(total_num)
            if(total_num == 0):
                num = random.uniform(float(info[6]), float(info[7]))
            else:
                num = total_num
            print 'num:'+ str(num)
            num = round(num, 8) #取8位小数

            price = round(price, 8)
            print 'price:'+ str(price)

            textmod={"access_token":str(info[0]),"market":marketinfo[int(info[3])],"side":paytype,"amount":str(num),"pride":str(price)}
            
            print textmod
            textmod = urllib.urlencode(textmod)
            url='http://huibi.xyz/api/exchange/order-limit'
            req = urllib2.Request(url=url,data=textmod)
            res = urllib2.urlopen(req)
            res = res.read()
            print(res)
    else:
        print u'may be error ...'

if __name__ == '__main__':
    while True:
        try:
            update_huobi()
            conn= Conn()
            cur=conn.cursor()
            lastUpdateTime(cur)
            cur.close()
            conn.close()
            time.sleep(0.1)
        except Exception,ex:
            print Exception,":",ex
            time.sleep(0.1)
