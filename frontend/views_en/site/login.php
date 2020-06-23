<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\ActiveForm;

?>


<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Log in:</p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <div style="color:#999;margin:1em 0">
                If you forget your password <?= Html::a('Reset', ['site/request-password-reset']) ?>.
            </div>
            <div class="form-group">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-5">
            <h5 style="margin-left: 35px">Other login way</h5>
            <?php $authAuthChoice = AuthChoice::begin([
                'baseAuthUrl' => ['site/auth'],
                'popupMode' => true,
            ]); ?>
            <ul class="auth-clients">
                <?php foreach ($authAuthChoice->getClients() as $client): ?>
                    <li><?= $authAuthChoice->clientLink($client,'',[ 'class' => 'auth-icon fa fa-2x fa-'.$client->getId()]) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php AuthChoice::end(); ?>
        </div>
    </div>
</div>