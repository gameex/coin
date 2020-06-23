<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 11:14
 */


namespace common\models;


use yii\db\ActiveRecord;

class OtcCoinList extends ActiveRecord{


    public static function tableName(){
        return "{{%otc_coinlist}}";
    }

    public function rules(){
        return [
            [['max_register_time','max_register_num','coin_name'],'required'],
            [['limit_amount'], 'number'],
            [['status'],'integer'],
            [['max_register_time','max_register_num'],'number'],
        ];
    }


    public function attributeLabels(){
        return [
            'id'                    =>      '主键ID',
            'coin_id'               =>      '币种ID',
            'coin_name'             =>      '币种名称',
            'limit_amount'          =>      '最低提现数量',
            'max_register_time'     =>      '最大挂单时间',
            'max_register_num'      =>      '最大挂单数量',
            'status'                =>      '状态',
        ];
    }

}