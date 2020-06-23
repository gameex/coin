<?php
namespace common\models;

use yii\db\ActiveRecord;

class MemberWealthBalance extends ActiveRecord{


    public static function tableName(){
        return "{{%member_wealth_balance}}";
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