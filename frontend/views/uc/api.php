<link href="/resource/frontend/css/uc.css" rel="stylesheet">  
<div id="main">
    <div class="main_box">
        <?php echo $this->render('left.php'); ?>
        <div class="raise right bg_w clearfix" >
            <div class="ybc_list">
                <div class="ybcoin clearfix">
                    <h2 class="left" data-i18n="u_c_1">API管理</h2>
                </div> 
                <ul class="api-explain" style="margin-left:30px;">
                    <li><span>1. API使用方法详见API文档</span>&nbsp;<a href="<?= Yii::$app->config->info('WEB_LINK_API') ?>" target="_blank">API文档&gt;</a></li>
                    <li><span>2. 更多问题可加入电报群交流：</span>&nbsp;<a href="<?= Yii::$app->config->info('WEB_LINK_TELEGRAM') ?>" target="_blank">Telegram</a></li>
                    <li><span>3. 由于API Key涉及到交易功能，请勿将此API Key透露给他人或保存在网络上以免账户被盗。</span></li>
                <div class="link-target">
                    <ul>
                        <li>
                            <span class="api_label">API Key</span>
                            <span class="link"></span>
                            <button class="wl-btn link-btn copy-text">复制API Key</button>
                        </li>
                    </ul>
                </div>                            

        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript" src="/resource/frontend/js/clipboard.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        http.post('user/api-key', {
        }, function(res) {
            $(".link").html(res.data.access_token);
            $('.link-btn').attr('data-clipboard-text',res.data.access_token);
        });

        var clipboard =  new ClipboardJS('.copy-text', {
            text: function(trigger) {
                 return trigger.getAttribute('data-clipboard-text');
            }
        });
        clipboard.on('success', function(e) {
             http.info('复制成功')
        });


    });   
</script>