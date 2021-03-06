# yii2-easy-wechat

> 基于最新的 overtrue/wechat 4.x

本项目由于[max-wen/yii2-easy-wechat](https://github.com/max-wen/yii2-easy-wechat) 不支持 [overtrue/wechat](https://github.com/overtrue/wechat) 4.0 改造而成

WeChat SDK for Yii2 , 基于 [overtrue/wechat](https://github.com/overtrue/wechat).     
这个扩展可以简单的用yii2的方式去调用EasyWechat:   `Yii::$app->wechat`.   

[![Latest Stable Version](https://poser.pugx.org/jianyan74/yii2-easy-wechat/v/stable)](https://packagist.org/packages/jianyan74/yii2-easy-wechat)
[![Total Downloads](https://poser.pugx.org/jianyan74/yii2-easy-wechat/downloads)](https://packagist.org/packages/jianyan74/yii2-easy-wechat)
[![License](https://poser.pugx.org/jianyan74/yii2-easy-wechat/license)](https://packagist.org/packages/jianyan74/yii2-easy-wechat)

## 安装
```
composer require jianyan74/yii2-easy-wechat
```

## 配置

添加 SDK 到Yii2的 `config/main.php` 的 `component`:

```php

'components' => [
	// ...
	'wechat' => [
		'class' => 'jianyan\easywechat\Wechat',
		// 'userOptions' => []  # 用户身份类参数
		// 'sessionParam' => '' # 微信用户信息将存储在会话在这个密钥
		// 'returnUrlParam' => '' # returnUrl 存储在会话中
	],
	// ...
]
```

设置基础配置信息和微信支付信息到 `config/params.php`:
```php
// 微信配置 具体可参考EasyWechat 
'wechatConfig' => [],

// 微信支付配置 具体可参考EasyWechat
'wechatPaymentConfig' => [],

// 微信小程序配置 具体可参考EasyWechat
'wechatMiniProgramConfig' => [],
```

[微信配置说明文档.](https://www.easywechat.com/docs/master/zh-CN/official-account/configuration)  
[微信支付配置说明文档.](https://www.easywechat.com/docs/master/zh-CN/payment/jssdk)  
[微信小程序配置说明文档.](https://www.easywechat.com/docs/master/zh-CN/mini-program/index)

## 使用例子

微信网页授权

```php
if(Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) 
{
    return Yii::$app->wechat->authorizeRequired()->send();
}
```
获取微信SDK实例

```php
$app = Yii::$app->wechat->app();
```
获取微信支付SDK实例

```php
$payment = Yii::$app->wechat->payment();
```
获取微信小程序实例

```php
$miniProgram = Yii::$app->wechat->miniProgram();
```
微信支付(JsApi):

```php
// 支付参数
$orderData = [ 
    'openid' => '.. '
    // ... etc. 
];

// 生成支付配置
$payment = Yii::$app->wechat->payment();
$result = $payment->order->unify($orderData);
if ($result['return_code'] == 'SUCCESS')
{
    $prepayId = $result['prepay_id'];
    $config = $payment->jssdk->sdkConfig($prepayId);
}
else
{
    throw new yii\base\ErrorException('微信支付异常, 请稍后再试');
}  

return $this->render('wxpay', [
    'jssdk' => $app->jssdk, // $app通过上面的获取实例来获取
    'config' => $config
]);

```

JSSDK发起支付
```
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    //数组内为jssdk授权可用的方法，按需添加，详细查看微信jssdk的方法
    wx.config(<?php echo $jssdk->buildConfig(array('chooseWXPay'), true) ?>);
    // 发起支付
    wx.chooseWXPay({
        timestamp: <?= $config['timestamp'] ?>,
        nonceStr: '<?= $config['nonceStr'] ?>',
        package: '<?= $config['package'] ?>',
        signType: '<?= $config['signType'] ?>',
        paySign: '<?= $config['paySign'] ?>', // 支付签名
        success: function (res) {
            // 支付成功后的回调函数
        }
    });
</script>
```

### 更多的文档

 [EasyWeChat Docs](https://www.easywechat.com/docs/master).

### 问题反馈

在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

QQ群：[655084090](https://jq.qq.com/?_wv=1027&k=4BeVA2r)

