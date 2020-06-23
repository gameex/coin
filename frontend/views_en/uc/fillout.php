<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>Merchant certification</title>
    <style>
        html{font-size: 100px;}
        *{margin:0;padding:0;font-family: pingFang-SC-Regular;list-style: none;}
        #box{
            width:100%;
            height:100%;
            background:#f4f4f4;
            /*overflow: hidden;*/
        }
        .content{width:100%;height:calc(100% - 0.24rem);background: #fff;position: relative;top:0.24rem;}
        .content img{width:1.82rem;height:1.7rem;margin-top: 2rem;margin-left: calc(50% - 0.91rem);position: absolute;}
        .tip{width:100%;font-size: 0.32rem;color:#757575;text-align:center;position: absolute;top:4.2rem;}
        .jump{width:2.8rem;height:0.8rem;background:#12a9ed;font-size: 0.28rem;color:#fff;position: absolute;top:5rem;left:calc(50% - 1.4rem);text-align: center;line-height: 0.8rem;border-radius: 0.2rem;text-decoration: none;display: none;}
    </style>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script>
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
                var img = '';
                var video = '';
                var describe = '';
                var token = localStorage.getItem('access_token');
                oBox.style.height = h;
                init();
                function init(){
                    $.ajax({
                        url:"/api/merchants/info",
                        type:"POST",
                        data:{access_token:token},
                        success : function(result) {
                            result = $.parseJSON(result)
                            if(result.code == 200){
                                if(result.data.otc_merchant == 1){
                                    $('.content img').attr('src','/resource/frontend/img/img_renzhengshenhezhong.png');
                                }else if(result.data.otc_merchant == 2){
                                    $('.content img').attr('src','/resource/frontend/img/img_renzhengchenggong.png');
                                }else if(result.data.otc_merchant == 3){
                                    $('.content img').attr('src','/resource/frontend/img/img_renzhengshibai.png');
                                    $('.jump').css({'display':'block'})
                                }
                                 $('.tip').html(result.data.otc_merchant_msg)
                                 // describe = result
                            }
                        }
                    });
                }
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
                var language = req['language'];
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
            <img src="" alt="">
            <p class="tip"></p>
            <a href="bussiness" class="jump">Re certification</a>
        </div>
    </div>
</body>
</html>