<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>Turn out</title>
    <style>
        html{font-size: 100px;}
        *{margin:0;padding:0;font-family: pingFang-SC-Regular;list-style: none;}
        #box{
            width:100%;
            height:100%;
            background:#ffffff;
            overflow: hidden;
        }
        .content{width:100%;height:100%;position: relative;}
        .header{width:calc(100% - 0.6rem);height:0.76rem;background:#ffffff;margin-left:0.3rem;}
        .header li{width:calc(100% / 3);height:100%;font-size:0.28rem;color:#757575;float: left;text-align: center;line-height: 0.76rem;}
        .header .active{border-bottom:1px solid #12a9ed; color:#12a9ed;}
        .spacing{width:100%;height:0.2rem;background:#f4f4f4;}
        .main{position: relative;}
        .main li{width:100%;position: relative;display: none;}
        .main li div{position: relative;margin:0.44rem 0.3rem 0 0.32rem;height:1.14rem;border-radius:0.04rem;}
        .main li input{height:1.14rem;width:100%;background:#f4f4f4;height:1.14rem;position: absolute;top:0;left:0;border: 0px;outline: none;border-radius:0.04rem;text-indent: 1em;font-size: 0.32rem;color:#bdbdbd;}
        input::-webkit-input-placeholder {
            color:#bdbdbd;
        }
        .main .tabActive{display: block;}
        .footer{width:100%;height:1rem;position: absolute;bottom: 0;left:0;background:#12a9ed;color:#ffffff;font-size: 0.36rem;line-height: 1rem;text-align: center;}
    </style>
    <script type="text/javascript" src="/resource/frontend/js/jquery.min.js"></script>
    <script>
        var count = 0;
        (function (doc, win) {
            var docEl = doc.documentElement
            var resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize'
            var recalc = function () {
                var clientWidth = docEl.clientWidth;
                if (!clientWidth) return;
                if (clientWidth >= 750) {
                    docEl.style.fontSize = '100px';
                } else {
                    docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
                }

                h = document.documentElement.clientHeight+'px';
                var oBox = document.getElementById('box');
                oBox.style.height = h;

                function GetRequest() {
                    var url = location.search; //获取url中"?"符后的字串
                    var theRequest = new Object();
                    if (url.indexOf("?") != -1) {
                        var str = url.substr(1);
                        strs = str.split("&");
                        for(var i = 0; i < strs.length; i ++) {
                            theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
                        }
                    }
                    return theRequest;
                }

                var req = GetRequest();

                var token = req['access_token'];
                var subtype = req['subtype'];
                

                var header = document.querySelector('.header');
                var aLi = header.children;
                var main = document.querySelector('.main');
                var oLi = main.children;
                for(var i =0;i<aLi.length;i++){
                    aLi[i].index = i;
                    aLi[i].onclick = function(){
                        now = this.index;
                        $('input').val('');
                        count = now;
                        tab(now);
                    }
                }
                function tab(now){
                    for(var i=0;i<aLi.length;i++){
                        aLi[i].className = '';
                        oLi[i].className = '';
                    }
                    
                    aLi[now].className = 'active';
                    oLi[now].className = 'tabActive';
                }
                function init(wallet_card,wallet_name,phone,value,description){
                    $.ajax({
                        url:"/api/bankout/turn-card",
                        type:"POST",
                        data:{
                            access_token:token,
                            coin_symbol:subtype,
                            wallet_card:wallet_card,
                            wallet_name:wallet_name,
                            phone:phone,
                            value:value,
                            description:description
                        },
                        success : function(result) {
                            result = $.parseJSON(result)
                            if(result.code == 200){
                                Toast(result.message,2000);
                            }else{
                                Toast(result.message,2000);
                            }
                        }
                    });
                }
 
                $('.content').off('click','.footer').on("click",'.footer',function(){

                    var aLi = $('.main').children("li").eq(count);
                    var walletC = aLi.children('.wallet_card');
                    var walletN = aLi.children('.wallet_name');
                    var outV = aLi.children('.outValue');
                    var phoneT = aLi.children('.phone');
                    var descrip = aLi.children('.description');

                    var wallet_card = walletC.children('input').val();
                    var wallet_name = walletN.children('input').val();
                    var outValue = outV.children('input').val();
                    var phone = phoneT.children('input').val();
                    var description = descrip.children('input').val();

                    if(count == 1){
                        wallet_name = "支付宝";
                        init(wallet_card,wallet_name,phone,outValue,description);
                    }else if(count == 2){
                        wallet_name = "微信";
                        init(wallet_card,wallet_name,phone,outValue,description);
                    }else if(count == 0){
                        wallet_name = walletN.children('input').val();
                        init(wallet_card,wallet_name,phone,outValue,description);
                    }
                    // e.stopPropagation();
                })
                function Toast(msg,duration){
                    duration=isNaN(duration)?3000:duration;
                    var m = document.createElement('div');
                    m.innerHTML = msg;
                    m.style.cssText="width: 60%;min-width: 150px;opacity: 0.7;height: 30px;color: rgb(255, 255, 255);line-height: 30px;text-align: center;border-radius: 5px;position: fixed;top: 40%;left: 20%;z-index: 999999;background: rgb(0, 0, 0);font-size: 12px;";
                    document.body.appendChild(m);
                    setTimeout(function() {
                        var d = 0.5;
                        m.style.webkitTransition = '-webkit-transform ' + d + 's ease-in, opacity ' + d + 's ease-in';
                        m.style.opacity = '0';
                        setTimeout(function() { document.body.removeChild(m) }, d * 1000);
                    }, duration);
                }
            };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
    </script>
</head>
<body>
    <div id="box">
        <div class="content">
            <ul class="header">
                <li class="active">银行卡</li>
                <li>支付宝</li>
                <li>微信</li>
            </ul>
            <div class="spacing"></div>
            <ul class="main">
                <li class="tabActive">
                    <div style="margin-top:0.32rem;" class="wallet_card">
                        <input type="text" placeholder="请输入您的银行卡号"  value="">
                    </div>
                    <div class="wallet_name">
                        <input type="text" placeholder="请输入银行名称">
                    </div>
                    <div  class="outValue">
                        <input type="text" placeholder="转出金额（最小金额：100RCNY）">
                    </div>
                    <div class="phone">
                        <input type="text" placeholder="请输入您的手机号">
                    </div>
                    <div class="description">
                        <input type="text" placeholder="备注（选填）">
                    </div>
                </li>
                <li>
                    <div class="wallet_card">
                        <input type="text" placeholder="请输入您的支付宝账号" >
                    </div>
                    <div class="outValue">
                        <input type="text" placeholder="转出金额（最小金额：100RCNY）">
                    </div>
                    <div class="phone">
                        <input type="text" placeholder="请输入您的手机号">
                    </div>
                    <div class="description">
                        <input type="text" placeholder="备注（选填）">
                    </div>
                </li>
                <li>
                    <div  class="wallet_card">
                        <input type="text" placeholder="请输入您的微信账号">
                    </div>
                    <div class="outValue">
                        <input type="text" placeholder="转出金额（最小金额：100RCNY）">
                    </div>
                    <div class="phone">
                        <input type="text" placeholder="请输入您的手机号">
                    </div>
                    <div class="description">
                        <input type="text" placeholder="备注（选填）">
                    </div>
                </li>
            </ul>
            <div class="footer">提交</div>
        </div>
    </div>
</body>
</html>