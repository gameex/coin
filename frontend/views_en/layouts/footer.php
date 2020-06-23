<style>
body{min-width:1400px}
</style>  
<?php
        $hide_footer_page = array('risk','login','reg');
    
    	$relative_footer_page = array('uc','sellbtc','trade');
    	   	
   	    if(in_array(Yii::$app->controller->id, $relative_footer_page))
    	
    		$position_value = "relative";
    		
    	else
			
			$position_value = "absolute";	

    ?>

<?php if(!in_array(Yii::$app->controller->id, $hide_footer_page)):?>


 <div class="newFt" style="background-color:#18195b;height: 430px;">
        <div class="newFtMain cl" style="padding:70px 100px;margin:auto;width:1140px;padding-bottom:0;display: inline-block;display: block;zoom: 1;">
            <div class="newFtMain_1" style="width:20%;margin-left:10%;float:left;">
                <p style="font-size:14px;color:#fff;line-height:40px;margin-bottom:10px;">About</p>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_ABOUT') ?>" data-i18n="a_about">About us</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_APP_DOWNLOAD') ?>" data-i18n="a_about">APP Download</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_NOTICE') ?>" data-i18n="a_about">Announcement</a>
            </div>
            <div class="newFtMain_2 z" style="width:20%;float:left;">
                <p style="font-size:14px;color:#fff;line-height:40px;margin-bottom:10px;">Clause description</p>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_AGREEMENT') ?>" data-i18n="a_clause">Terms of Use</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_PRIVACY') ?>" data-i18n="a_policy">Privacy policy</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_ANTI') ?>" data-i18n="a_rules">Anti-money laundering Rule</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_FEE') ?>" data-i18n="a_rate">Rate specification</a>
            </div>
            <div class="newFtMain_3 z" style="width:20%;margin-left:10%;float:left;">
                <p style="font-size:14px;color:#fff;line-height:40px;margin-bottom:10px;">Service support</p>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_HELP') ?>" data-i18n="help">Service support</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_REQUEST') ?>">Submit requests</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_NEW_COIN') ?>" data-i18n="apply">Apply to listing</a>
            </div>
            <div class="newFtMain_4 z" style="width:20%;float:left;">
                <p style="font-size:14px;color:#fff;line-height:40px;margin-bottom:10px;">Contact us</p>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank" href="<?= Yii::$app->config->info('WEB_LINK_TWITTER') ?>">Twitter</a>
                <a style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank"href="<?= Yii::$app->config->info('WEB_LINK_TELEGRAM') ?>">Telegram</a>
                <p style="display: block;color:#d9d9db;font-size:12px;letter-spacing: 2px;margin-bottom:10px;" target="_blank"href=""><?= Yii::$app->config->info('WEB_EMAIL') ?></p>
            </div>
        </div>
       <div style='float:left;width:100%;'>
		   <img src="<?= Yii::$app->config->info('WEB_SITE_LOGO_BOTTOM') ?>" style="height:70px;display: block;margin:25px auto;margin-bottom:0;" alt="">
          <p style="text-align:center;font-size:14px;line-height:40px;letter-spacing:2px;color:#fff;"><?= Yii::$app->config->info('WEB_SITE_ICP') ?><?= Yii::$app->config->info('WEB_VISIT_CODE') ?></p>
          <p style="text-align:center;font-size:14px;line-height:40px;letter-spacing:2px;color:#fff;padding-bottom:20px;"><?= Yii::$app->config->info('WEB_COPYRIGHT_ALL') ?></p>
       </div>
    </div>



<?php endif?>
