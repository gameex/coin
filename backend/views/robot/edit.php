<?php
use yii\widgets\ActiveForm;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '交易列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$market_arr = api\models\ExchangeCoins::find()->select(['id','stock','money'])->asArray()->all();
foreach ($market_arr as $value) {
   $market_list[$value['id']] = $value['stock'].'/'.$value['money'];
}
/*
dropDownList 三个参数 1名称 2option value=>显示值 3.最上面头
*/
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>编辑交易</h5>
                </div>
                <div class="ibox-content">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <?= 
                            $form->field($model, 'market_id')
                            ->dropDownList($market_list,
					    ['prompt'=>'无']
                            )
                        ?>

                        <?= $form->field($model, 'uid')->textInput() ?>
                        <?= $form->field($model, 'intime')->textInput() ?> 
                        <?= $form->field($model, 'small_money')->textInput() ?>
                        <?= $form->field($model, 'big_money')->textInput() ?>
                        <?= $form->field($model, 'small_count')->textInput() ?>
                        <?= $form->field($model, 'big_count')->textInput() ?>
                        <?= $form->field($model, 'otime')->textInput() ?>
                        <?= $form->field($model, 'ctime')->textInput() ?>
                        <?= $form->field($model, 'status')->dropDownList(['1' => '启用','0' => '禁用']) ?>
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