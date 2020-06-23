<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>购买套餐</title>
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
        .header{width:calc(100% - 0.6rem);height:1.14rem;background:#ffffff;margin-left:0.3rem;}
        .header li{width:calc(100% / 3);height:100%;font-size:0.28rem;color:#757575;float: left;text-align: center;line-height: 0.76rem;}
        .header .active{border-bottom:1px solid #12a9ed; color:#12a9ed;}
        .spacing{width:100%;height:1px;background:#f4f4f4;}
        .main{position: relative;}
        .main li{width:100%;position: relative;}
        .main li div{position: relative;margin:0.44rem 0.3rem 0 0.32rem;height:1.14rem;border-radius:0.04rem;}
        .main li input{height:1.14rem;width:70%;background:#f4f4f4;height:1.14rem;position: absolute;top:0;right:0;border: 0px;outline: none;border-radius:0.04rem;text-indent: 1em;font-size: 0.32rem;color:#bdbdbd;}
        input::-webkit-input-placeholder {
            color:#bdbdbd;
        }
        .main .tabActive{display: block;}
        .footer{
            bottom: 0;
            left: 0;
            background: #12a9ed;
            color: #ffffff;
            font-size: 0.36rem;
            line-height: 1.14rem;
            text-align: center;
            margin: 0.34rem 0.3rem 0 0.32rem !important;
        }
        label{
            width:30%;
            font-size: 0.32rem;
            line-height: 1.14rem;
            height: 1.14rem;
            display: block;
        }
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

                $('.content').off('click','.footer').on("click",'.footer',function(){

                    var id = $('#id').val();
                    var num = $('#num').val();

                    $.ajax({
                        url:"/api/user/weathbuypackage",
                        type:"POST",
                        data:{
                            access_token:token,
                            chain_network:'main_network',
                            os:'web',
                            id:id,
                            num:num,
                        },
                        success : function(result) {
                            result = $.parseJSON(result)
                            if(result.code == 200){
                                Toast(result.message,2000);
                                window.location.href="/wap/wealthlock?access_token="+token;
                            }else{
                                Toast(result.message,2000);
                            }
                        }
                    });

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
                <label>购买套餐</label></li>
            </ul>
            <div class="spacing"></div>
            <ul class="main">
                <li>
                    <div>
                        <label>币种：</label>
                        <input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['coin_symbol'] ?>">
                    </div>
                </li>
                <li>
                    <div>
                        <label>名称：</label>
                        <input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['name'] ?>">
                    </div>
                </li>                <li>
                    <div>
                        <label>有效期：</label>
                        <input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['period'] ?>">
                    </div>
                </li>                <li>
                    <div>
                        <label>日收益：</label>
                        <input name="id" id="id" type="hidden" readonly="readonly" value="<?= $wealtdetail['id'] ?>">
                        <input name="email" id="email" type="text" readonly="readonly" value="<?= $wealtdetail['day_profit'] ?>">
                    </div>
                </li>                <li>
                    <div>
                        <label>购买额度：</label>
                        <input name="num" id="num" type="text" value="" placeholder="最低购入<?= $wealtdetail['min_num'] ?><?= $wealtdetail['coin_symbol'] ?>">
                    </div>
                </li>
                <li>
                    <div class="footer"  id="tijiao" style="">提交</div>
                </li>
                <li>
                    <div class="footer"  onclick="window.history.back(-1);" style="    margin-top: 0.2rem !important;">返回</div>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>