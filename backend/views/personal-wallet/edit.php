<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '个人钱包管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>个人钱包</h5>
                </div>

                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'coin_id')->dropdownList($coins_list) ?>
                        <?= $form->field($model, 'addr')->textInput() ?>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <?= Html::submitButton('保存内容', ['class' => 'btn btn-primary']) ?>
                            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
