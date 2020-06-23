<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%exchange_rate}}".
 *
 * @property int $id
 * @property string $coin_symbol 货币标识
 * @property string $usd 兑换美元汇率
 * @property string $cny 兑换人民币汇率
 * @property int $created_at 汇率更新时间
 */
class ExchangeRate extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exchange_rate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coin_symbol', 'usd', 'cny'], 'required'],
            [['usd', 'cny'], 'number'],
            [['created_at'], 'integer'],
            [['coin_symbol'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coin_symbol' => 'Coin Symbol',
            'usd' => 'Usd',
            'cny' => 'Cny',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 行为插入时间戳
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['created_at'],
                ],
            ],
        ];
    }
}
