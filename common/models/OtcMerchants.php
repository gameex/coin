<?php
namespace common\models;

use yii\db\ActiveRecord;

class OtcMerchants extends ActiveRecord{


    public static function tableName(){
        return "{{%otc_merchants}}";
    }

    public function rules(){
        return [
            
        ];
    }


    public function attributeLabels(){
        return [

        ];
    }

}