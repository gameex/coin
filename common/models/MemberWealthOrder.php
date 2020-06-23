<?php
namespace common\models;

use yii\db\ActiveRecord;

class MemberWealthOrder extends ActiveRecord{


    public static function tableName(){
        return "{{%member_wealth_order}}";
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