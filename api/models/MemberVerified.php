<?php

namespace api\models;

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
class MemberVerified extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_verified}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'real_name', 'id_number', 'id_card_img', 'id_card_img2', 'ctime'], 'required'],
            [['uid', 'status'], 'integer'],
            [['ctime', 'audit_time'], 'safe'],
            [['real_name', 'id_number'], 'string', 'max' => 100],
            [['id_card_img', 'id_card_img2'], 'string', 'max' => 255],
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
            'real_name' => 'Real Name',
            'id_number' => 'Id Number',
            'id_card_img' => 'Id Card Img',
            'id_card_img2' => 'Id Card Img2',
            'status' => 'Status',
            'ctime' => 'Ctime',
            'audit_time' => 'Audit Time',
        ];
    }
}
