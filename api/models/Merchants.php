<?php

namespace api\models;

use common\models\member\Member;
use Yii;

/**
 * This is the model class for table "{{%member_verified}}".
 *
 * @property string $id
 * @property string $uid 用户id
 * @property string $real_name 真实姓名
 * @property string $id_number 身份证号
 * @property string $id_card_img 身份证照片
 * @property string $id_card_img2
 * @property int $status 0 已删除 1已经提交 2审核通过 3审核未通过
 * @property int $ctime 提交时间
 * @property int $audit_time 审核时间
 */
class Merchants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%otc_merchants}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'image', 'describe', 'video', 'created_at'], 'required'],
            [['uid', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image', 'describe', 'video'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'image' => '图片',
            'describe' => '描述',
            'video' => '视频',
            'status' => 'Status',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id' => 'uid']);
    }
}
