<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:29
 */

namespace common\models;

use yii\db\ActiveRecord;

class OtcOrder extends ActiveRecord{

    public static function tableName(){
        return "{{%otc_order}}";
    }


}