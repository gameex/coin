<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:29
 */

namespace common\models;

use yii\db\ActiveRecord;
use common\models\member\Member;
use Yii;

class OtcAppeal extends ActiveRecord{

    public static function tableName(){
        return "{{%otc_appeal}}";
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'image', 'describe', 'order_id', 'created_at'], 'required'],
            [['uid', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image', 'describe'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'uid' => '用户ID',
            'image' => '图片',
            'describe' => '描述',
            'status' => 'Status',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id' => 'uid']);
    }


}