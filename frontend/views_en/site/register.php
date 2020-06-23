<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
?>
<div class="site-signup">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'email') ?>

            <?= $form->field($model, 'password')->passwordInput() ?>
            <div style="color:#999;margin:1em 0">
                 If you already have an account <?= Html::a('Log in', ['site/login']) ?>.
            </div>
            <div class="form-group">
                <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>