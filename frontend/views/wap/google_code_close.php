<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>转出</title>
    <style>
        *{margin:0;padding:0;font-family: pingFang-SC-Regular;list-style: none;font-size: 16px;}
        #box{
            width:100%;
            height:100%;
            background:#ffffff;
            overflow: hidden;
        }
        .content{width:100%;height:100%;position: relative;}
		.header {
		    background: #ffffff;
		    text-align: center;
		    line-height: 50px;
		    color: #666;
		}
        .header li{width:calc(100% / 3);height:100%;font-size:0.28rem;color:#757575;float: left;text-align: center;line-height: 0.76rem;}
        .header .active{border-bottom:1px solid #12a9ed; color:#12a9ed;}
        .spacing{width:100%;height:0.2rem;background:#f4f4f4;}
        .main{position: relative;}
        .main li{width:100%;position: relative;}
        .main li div{position: relative;margin:0.44rem auto;border-radius:0.04rem;}
        .main li input{height:1.14rem;width:100%;background:#f4f4f4;height:1.14rem;border: 0px;outline: none;border-radius:0.04rem;text-indent: 1em;font-size: 0.32rem;color:#bdbdbd;}
        input::-webkit-input-placeholder {
            color:#bdbdbd;
        }
        .main .tabActive{display: block;}
        .footer{width:80%;height:1.14rem;position: absolute;bottom: 0;left:0;background:#12a9ed;color:#ffffff;font-size: 0.36rem;line-height: 1.14rem;text-align: center;}

		.btn {
		    background: none;
		    color: #1e86db;
		    outline: none;
		    border: 0;
		    line-height: 20px !important;
		}
        .info{
            padding: 0 5%;
        }
        a{
            text-decoration: none;
        }
        .erQur{
            width: 200px;
            height: 200px;
            margin-top: 36px;
            margin-left: calc(50% - 110px);
            border: 6px solid #dfeeff;
        }
        .address {
            color: #212529;
            width: 100%;
            text-align: center;
            margin-top: 36px;
            padding: 5px;
            font-size: 12px;
            border: 1px solid #eee;
            line-height: 30px;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }
        .tip{
            margin-top: 30px;
            width: 100%;
            color: #f14b4b;
            font-weight: bold;
        }
        .copy{
            width: 20px;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
    <script type="text/javascript" src="/resource/frontend/js/jquery.min.js"></script>
    <script type="text/javascript" src="/resource/frontend/js/jquery.qrcode.min.js"></script>
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



                var token = req['access_token'];
                var subtype = req['subtype'];
                

                // var header = document.querySelector('.header');
                // var aLi = header.children;
                // var main = document.querySelector('.main');
                // var oLi = main.children;
                // for(var i =0;i<aLi.length;i++){
                //     aLi[i].index = i;
                //     aLi[i].onclick = function(){
                //         now = this.index;
                //         $('input').val('');
                //         count = now;
                //         tab(now);
                //     }
                // }
                // function tab(now){
                //     for(var i=0;i<aLi.length;i++){
                //         aLi[i].className = '';
                //         oLi[i].className = '';
                //     }
                    
                //     aLi[now].className = 'active';
                //     oLi[now].className = 'tabActive';
                // }


            };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
    </script>
</head>
<body>
    <div id="box">
        <div class="content content1">
            <!-- <div class="header">开启谷歌二次验证</div> -->
            <div class="spacing"></div>
            <ul class="main">
                <li>
                    <div class="info" style="text-align: center;">
                        您已经成功开启谷歌两步验证
                    </div>
                    <div class="footer" onclick="close_open()">关闭</div>
					<div class="info">
                        谷歌两步验证可以为您的账户增加一层保护，当您登陆或者进行交易时，在输入密码的同时，还需要来自两步验证的验证码
					</div>
                    <div class="info">
                        <a data-v-574295bc="" href="http://etbank.kinlink.cn/portal/article/index/id/5.html"><p style="color: rgb(18, 150, 219);margin-top: 0px;">如何使用谷歌两步验证?</p></a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="content content2">
            <!-- <div class="header">谷歌二次验证</div> -->
            <div class="spacing"></div>
            <ul class="main">
                <li>
                    <div class="info">
                        <input type="text" id="google_code" placeholder="请输入谷歌验证码">
                    </div>
                    <div class="footer" style="width: 90%;"  onclick="close_google_code()">确认关闭</div>
                </li>
            </ul>
        </div>


    </div>

<script type="text/javascript">
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

    function close_open(){
        $('.content1').hide();
        $('.content2').show();
    }

    function close_google_code(){
        var code = $('#google_code').val();
        if (code == '') {
            Toast('验证码不能为空',2000);
            return;
        }
        $.ajax({
            url:"/api/register/close-google-check",
            type:"POST",
            data:{
                access_token:token,
                var_code:code,
            },
            success : function(result) {
                result = $.parseJSON(result)
                if(result.code == 200){
                    go_back_usercenter();
                }else{
                    Toast(result.message,2000);
                }
            }
        });

    }
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
    function go_back_usercenter(){
        var u = navigator.userAgent, app = navigator.appVersion;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //g
        var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        if (isAndroid) {
            window.android.go_back_usercenter();
        }
        if (isIOS) {
            window.webkit.messageHandlers.go_back_usercenter.postMessage("");
        }
    }


</script>
</body>
</html>