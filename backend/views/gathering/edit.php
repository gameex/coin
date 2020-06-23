<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '收款类型', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>类型信息</h5>
                </div>
                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'name')->textInput() ?>
                        <?= $form->field($model, 'proceeds_type')->textInput() ?>
                        <?= $form->field($model, 'icon')->widget('backend\widgets\webuploader\Image', [
                            'boxId' => 'icon',
                            'options' => [
                                'multiple'   => false,
                            ]
                        ])?>
                        <?= $form->field($model, 'is_qrcode')->dropDownList(['1' => '需要','0' => '不需要']) ?>
                        <?= $form->field($model, 'status')->dropDownList(['1' => '启用','0' => '关闭']) ?>
                        <div class="hr-line-dashed"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-primary" type="submit">保存内容</button>
                            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
