# -*- coding: utf-8 -*-
import time
import sys
import MySQLdb
import requests
import json
import os

def Check(cur,conn):
        assets_count = cur.execute("select symbol from jl_coins where enable = 1 order by listorder desc")
        print 'db have %d coins' % assets_count
        assets = [k[0] for k in cur.fetchall()]
        print assets

        assetss = (','.join("'%s'" % k for k in assets))

        markets_count = cur.execute("select stock,money,decimals from jl_exchange_coins where enable = 1 and stock in (%s) and money in (%s) order by listorder desc" % (assetss,assetss))
        print 'db have %d market' % markets_count
        old_markets = cur.fetchall()        
        markets = [k[0]+k[1] for k in old_markets]
        print markets
        
        with open("config.json",'r') as load_f:
                conf = json.load(load_f)

        #print conf
        conf_asset = conf['assets']
        conf_market = conf['markets']

        conf_asset_name = [k['name'] for k in conf_asset]
        print conf_asset_name

        new_assets = []
        new_market = []
        
        if assets != conf_asset_name:
                print 'asset !='                
                for x in assets:
                        new_assets.append({'name':x,'prec_save':20,'prec_show':8})
                conf['assets'] = new_assets

        conf_market_name = [k['name'] for k in conf_market]
        print conf_market_name

        if markets != conf_market_name:
                print 'market !='                
                for x in old_markets:
                        new_market.append({'money':{'name':x[1],'prec':x[2]},'name':x[0]+x[1],'min_amount':'0.001','stock':{'name':x[0],'prec':x[2]}})
                conf['markets'] = new_market                

        if new_assets or new_market:
                print 'need update...'
                #先备份原文件
                new_name = "config_%d.json" % int(time.time())
                print new_name
                os.rename("config.json", new_name)
                with open("config.json","w") as f:
                        json.dump(conf,f, indent=4)
                        print("加载入文件完成...")
                while True:
                        if os.system("sh restart.sh") == 0:
                                time.sleep(3)
                                #viaCheck()
                                break
                        else:                                
                                print "sh restart.sh"
                                time.sleep(0.5)

def rpc(method, params):
        host = "http://127.0.0.1:8080/"
        headers = {'Content-Type': 'application/json', 'Connection':'close'}
        post_data = {"jsonrpc":"2.0","method":method,"params":params,"id":1}
        encode_json = json.dumps(post_data)
        print encode_json
        try:
                P_post=requests.post(host,headers=headers,data=encode_json,timeout=5)
                if P_post.status_code == 200:
                        rst = json.loads(P_post.text)
                        print rst
                        if rst.has_key('result'):
                                return None if rst['result'] == '' else rst['result']
                        else:
                                return None
                else:
                        return None
        except requests.RequestException as e:
                print e
                return None

def viaCheck():
        markets = rpc('market.list',[])
        print markets
        for market in markets:                
                rst = rpc('market.last',[market['name']])
                if rst is None:#该市场未交易
                        print market
                        #0.给用户充对应余额
                        update1 = rpc('balance.update', [1,market['stock'], 'deposit',int(time.time()*1000),'1',{}])
                        print update1
                        if update1 is None:
                                break
                        if update1.has_key('status') and update1['status'] == 'success':
                                print '%s init balance.update %s 1 success' % (market['name'],market['stock'])
                        else:
                                break
                        update2 = rpc('balance.update', [1,market['money'], 'deposit',int(time.time()*1000),'1',{}])
                        print update2
                        if update2 is None:
                                break
                        if update2.has_key('status') and update2['status'] == 'success':
                                print '%s init balance.update %s 1 success' % (market['name'],market['money'])
                        else:
                                break
                        #1.先卖
                        sell = rpc('order.put_limit', [1,market['name'],1,'1','1','0.000001','0.000001','init'])
                        print sell
                        if sell is None:
                                print '%s init put_limit sell is None' % market['name']
                                break
                        if sell.has_key('id'):#下单成功
                                print '%s init put_limit sell 1 success' % market['name']
                        else:
                                print '%s init put_limit sell 1 fail' % market['name']
                        #2.买
                        buy = rpc('order.put_limit', [1,market['name'],2,'1','1','0.000001','0.000001','init'])
                        print buy
                        if buy is None:
                                print '%s init put_limit buy is None' % market['name']
                                break
                        if buy.has_key('id'):#下单成功
                                print '%s init put_limit buy 1 success' % market['name']
                        else:
                                print '%s init put_limit buy 1 fail' % market['name']


def Run():
        conn= Conn()
        cur=conn.cursor()
        Check(cur,conn)        
        cur.close()
        conn.close()

def Conn():
        return MySQLdb.connect(host='127.0.0.1',user='jinglanex8',passwd='jinglanex8',db='jinglanex8',port=3306, charset="utf8")
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
