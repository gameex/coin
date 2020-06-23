<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $user_openid
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password_reset_token
 * @property integer $type
 * @property string $nickname
 * @property string $realname
 * @property string $head_portrait
 * @property integer $sex
 * @property string $qq
 * @property string $email
 * @property string $birthday
 * @property string $user_money
 * @property string $accumulate_money
 * @property string $frozen_money
 * @property integer $user_integral
 * @property string $address_id
 * @property integer $visit_count
 * @property string $home_phone
 * @property string $mobile_phone
 * @property string $passwd_question
 * @property string $passwd_answer
 * @property integer $role
 * @property integer $status
 * @property integer $last_time
 * @property string $last_ip
 * @property integer $created_at
 * @property integer $updated_at
 */
class Recharges extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%recharges}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['coin_name','user_id','recharge_img','coin_num'], 'required']

        ];
    }
}
