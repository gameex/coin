<?php
$hide_footer_page = array('risk');
$relative_footer_page = array('uc','sellbtc','trade');
    if(in_array(Yii::$app->controller->id, $relative_footer_page))
	$position_value = "relative";
else
	$position_value = "absolute";	
?>
<?php if(!in_array(Yii::$app->controller->id, $hide_footer_page)):?>
 <div class="newFt">
        <div class="newFtMain cl" style="padding:70px 100px;margin:auto;width:1140px;padding-bottom:0;display: inline-block;display: block;zoom: 1;">
            <div class="newFtMain_1" style="width:20%;margin-left:10%;float:left;">
                <p class="foot_p">关于</p>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_ABOUT') ?>" data-i18n="a_about">关于我们</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_APP_DOWNLOAD') ?>" data-i18n="a_about">APP下载</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_NOTICE') ?>" data-i18n="a_about">公告</a>
            </div>
            <div class="newFtMain_2 z" style="width:20%;float:left;">
                <p class="foot_p">条款说明</p>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_AGREEMENT') ?>" data-i18n="a_clause">使用条款</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_PRIVACY') ?>" data-i18n="a_policy">隐私政策</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_ANTI') ?>" data-i18n="a_rules">反洗钱条例</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_FEE') ?>" data-i18n="a_rate">费率说明</a>
            </div>
            <div class="newFtMain_3 z" style="width:20%;margin-left:10%;float:left;">
                <p class="foot_p">服务支持</p>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_HELP') ?>" data-i18n="help">帮助</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_REQUEST') ?>">提交请求</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_NEW_COIN') ?>" data-i18n="apply">上币申请</a>
            </div>
            <div class="newFtMain_4 z" style="width:20%;float:left;">
                <p class="foot_p">联系我们</p>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_TWITTER') ?>">Twitter</a>
                <a class="foot_a" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_TELEGRAM') ?>">Telegram</a>
                <p class="foot_a" target="_blank" href=""><?= Yii::$app->config->info('WEB_EMAIL') ?></p>
            </div>
        </div>
       <div style='float:left;width:100%;'>
		  <img src="<?= Yii::$app->config->info('WEB_SITE_LOGO_BOTTOM') ?>" style="height:70px;display: block;margin:25px auto;margin-bottom:0;" alt="">
          <p class="foot_b_p"><?= Yii::$app->config->info('WEB_SITE_ICP') ?><?= Yii::$app->config->info('WEB_VISIT_CODE') ?></p>
          <p class="foot_b_p2"><?= Yii::$app->config->info('WEB_COPYRIGHT_ALL') ?></p>
       </div>
</div>
<?php endif?>
