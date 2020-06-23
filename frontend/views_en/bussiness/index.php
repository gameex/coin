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
            background:#f8f9fa;
            /*overflow: hidden;*/
        }
        .content{width:100%;height:100%;}
        .imgTitle{width:100%;height:0.7rem;line-height: 0.7rem;background:#f4f4f4;font-size:0.24rem;color:#757575;text-indent: 1em;}
        .imgContent{width:100%;background:#ffffff;margin-top: 0;}
        .oImg{width:4.92rem;height:2.6rem;border:1px solid #eaeaea;margin-left: calc(50% - 2.46rem);position: relative;top:0.24rem;border-radius: 8px;text-align: center;line-height: 2.6rem;}
        .oImg img{width:1rem;height:1rem;}
        .captureRequire{width:100%;margin-top: 0.4rem;font-size: 0.28rem;color:#12a9ed;text-indent: 1em;line-height: 0.4rem; }
        .captureDsc{margin:0 0.3rem;color:#757575;font-size: 0.24rem;line-height: 0.48rem;padding-bottom: 0.2rem;}
        .button{width:100%;height:0.9rem;background:#12a9ed;font-size:0.32rem;text-align:center;line-height:0.9rem;color:#ffffff;opacity: 0.6;}
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
                                if(result.data.otc_merchant == 0){
                                    $('#myVideo').css({'display':'block'});
                                }else if(result.data.otc_merchant == 3){
                                    img = result.data.image;
                                    video = result.data.video
                                    $('.picture img').attr('src',img);
                                    $('#myVideo').attr('src',video);
                                    $('.video img').css({'display':'none'});
                                    $('#myVideo').css({'display':'block'});
                                }
                            }else{
                                Toast(result.message,2000);
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
                var token = req['token'];

                var input = document.getElementById('uploadZ');
                input.onchange =function(e){
                    var dom =document.getElementById('uploadZ').files[0];
                    var reader = new FileReader();
                    reader.onload = (function (file) {
                        return function (e) {
                            $('.picture img').attr('src',this.result);
                            $('.picture img').css({'width':'100%','height':'100%'})
                        };
                })(e.target.files[0]);
                    reader.readAsDataURL(e.target.files[0]);
                    var formFile = new FormData($('#signupForm'));
                    formFile.append("access_token",token);
                    formFile.append("image",dom); //加入文件对象
                    var data = formFile;
                   $.ajax({
                       url: "/api/merchants/image",
                       data: data,
                       type: "Post",
                       
                       cache: false,//上传文件无需缓存
                       processData: false,//用于对data参数进行序列化处理 这里必须false
                       contentType: false, //必须
                       success: function (result) {
                            result = $.parseJSON(result)
                            if(result.code == 200){
                                img = result.data.urlPath;
                                return img
                            }else{
                                Toast(result.message,2000);
                            }
                       }
                   })
                }

                var upload = document.getElementById('upload');
                upload.onchange =function(e){
                    var dom =document.getElementById('upload').files[0];
                    var reader = new FileReader();
                    reader.onload = (function (file) {
                        return function (e) {
                            $('#myVideo').attr('src',this.result);
                            $('.video img').css({'display':'none'})
                            $('#myVideo').css({'display':'block'});
                        };
                })(e.target.files[0]);
                    reader.readAsDataURL(e.target.files[0]);
                    var formFile = new FormData($('#signupForm'));
                    formFile.append("access_token",token);
                    formFile.append("video",dom); //加入文件对象
                    var data = formFile;
                   $.ajax({
                       url: "/api/merchants/video",
                       data: data,
                       type: "Post",
                       dataType: "json",
                       cache: false,//上传文件无需缓存
                       processData: false,//用于对data参数进行序列化处理 这里必须false
                       contentType: false, //必须
                       success: function (result) {
                            if(result.code == 200){
                                video = result.data;
                                return video
                            }else{
                                Toast(result.message,2000);
                            }
                       },
                   })
                }

                $(".button").click(function(){
                    if(img!='' && video != ''){
                        console.log(img);
                        console.log(video)
                        $.ajax({
                            url:"/api/merchants/merchants",
                            type:"POST",
                            data:{"access_token":token,image:img,video:video,describe:'描述'},
                            success : function(result) {
                                result = $.parseJSON(result)
                                console.log(result)
                                if(result.code == 200){
                                    Toast(result.message,2000);
                                }else{
                                    Toast(result.message,2000);
                                }
                            }
                        });
                    }else{
                        if(img==''){
                            Toast('上传图片不能为空',2000);
                        }else if(video == ''){
                            Toast('上传视频不能为空',2000);
                        }else{
                            Toast('请填写完整信息',2000);
                        }
                    }
                });

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
            <p class="imgTitle">Please upload a screenshot of the asset certificate</p>
            <div class="imgContent">
                <div class="oImg picture">
                    <img src="/resource/frontend/img/ico_jietu_shangjiarenzheng.png" alt="">
                    <input type="file" id="uploadZ" style="position:absolute;top: 0;left: 0;width: 100%;height: 100%;opacity: 0;">
                </div>
                <p class="captureRequire">Screenshots requirements</p>
                <p class="captureDsc">My real-name bank card balance or virtual currency asset screenshots, or corresponding bank card for a month of bank flow screenshots.</p>
            </div>
            <p class="imgTitle">Please upload the recorded video.</p>
            <div class="imgContent">
                <div class="oImg video" style="position: relative;">
                    <img src="/resource/frontend/img/ico_shipin_shangjiarenzheng.png" alt="">
                    <input type="file" accept="video/*"  id="upload" style="position:absolute;top: 0;left: 0;width: 100%;height: 100%;opacity: 0;z-index: 10;"/>
                    <video id="myVideo" controls="controls" autoplay loop style="width:100%;height:100%;position: absolute;top:0;left:0;display: none;"/>
                </div>
                <p class="captureRequire">Video request</p>
                <p class="captureDsc" style="padding-bottom: 0;">1.Hold the front of the ID card and record the video. Keep the sound and image clear during the recording process.</p>
                <p class="captureDsc">2.Video recitation model: I (name), ID number, legal and reliable sources of funds, voluntary trading of digital assets such as Bitcoin, I fully understand the digital currency and potential risks, I have the ability to resist risks and willing to bear all risks!</p>
            </div>
            <div class="button">Complete</div>
        </div>
    </div>
</body>
</html>