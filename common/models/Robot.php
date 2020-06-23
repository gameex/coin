<?php

namespace common\models;

use api\models\ExchangeCoins;
use Yii;

/**
 * This is the model class for table "jl_start_page".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $img 图片路径
 * @property string $url 链接
 * @property int $status 启用状态,默认0未启用,1启用,2已删除
 * @property int $add_time 添加时间
 */
class Robot extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jl_robot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['market_id', 'small_money','big_money','big_count','small_count','otime','ctime','intime'], 'required'],
            [['uid', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'market_id' => '交易市场',
            'market' => '交易市场',
            'small_money' => '最小交易价格',
            'big_money' => '最大交易价格',
            'small_count' => '最小交易数量',
            'big_count' => '最大交易数量',
            'intime' => '间隔时间',
            'status' => '状态',
            'otime' => '开始交易时间',
            'ctime' => '关闭交易时间',
        ];
    }

    public function getExchangeCoins(){
        return $this->hasOne(ExchangeCoins::className(),['id' => 'market_id']);
    }
}