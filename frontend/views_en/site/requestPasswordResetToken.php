<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Retrieve password';
?>
<div class="site-request-password-reset">

    <p>Please fill out your email. A link to reset password will be sent there.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Retrieve immediately', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
