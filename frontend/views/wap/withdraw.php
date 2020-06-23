<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>提现</title>
    <style>
        html{font-size: 100px;}
        *{margin:0;padding:0;font-family: pingFang-SC-Regular;list-style: none;}
        #box{
            width:100%;
            height:100%;
            background:#ffffff;
        }
        .content{width:100%;position: relative;}
        .main{position: relative;margin-top:0.2rem;background:#ffffff;}
        .main li{width:100%;position: relative;border-bottom: 1px solid #eeeeee;}
        .oImg{width:0.32rem;height:0.32rem;position:absolute;top:0.4rem;left:0.3rem;}
        .bankName{font-size:0.28rem;color:#212121;position:absolute;top:0.4rem;left:0.82rem;}
        .addTime{font-size: 0.24rem;color:#757575;position: absolute;top:0.4rem;right:0.3rem;}
        .phone{font-size:0.24rem;color:#757575;top:0.85rem;margin-left: 0.82rem;position: relative;}
        .monMrak{font-size:0.36rem;color:#212121;display: inline-block;margin:1rem 0 0.44rem 0.82rem;font-family:PingFang-SC-Bold;}
        .state{font-size:0.28rem;color:#12a9ed;position: absolute;right: 0.3rem;bottom:0.44rem;}
    </style>
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

                // var language = req['language'];
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
            <ul class="main">
                <?php if($withdraw_apply){ ?>
                    <?php foreach($withdraw_apply as $model){ ?>
                        <li>
                            <img src="/resource/frontend/img/icon_yihangka.png" alt="" class="oImg">
                            <span class="bankName"><?= $model['wallet_name']?></span>
                            <span class="addTime"><?= date('Y-m-d H:i:s', $model['created_at'])?></span>
                            <div style="clear:both"></div>
                            <p class="phone">手机号：<?= $model['phone']?></p>
                            <span class="monMrak"><?= $model['value_dec']?> RCNY</span>
                            <span class="state"><?= $apply_status[$model['status']]?></span>
                        </li>
                    <?php } ?>
                <?php }else{ ?>
                    <br />
                    <br />
                    <br />
                    <p style="font-size: .5em;text-align: center;background-color: #ffffff;">暂无数据！</p>
                    <br />
                    <br />
                    <br />
                <?php } ?>
            </ul>
        </div>
    </div>
</body>
</html>