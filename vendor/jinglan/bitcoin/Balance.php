<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-06-26
 * Time: 17:32
 */

namespace jinglan\bitcoin;

//use jinglan\bitcoin\Unspent;

class Balance
{
    /*请求*/
    public function getbalance($address){
        $obj = new Unspent();
        $data = $obj->do_curl($address);

        if($data['code'] == 1){
            $code = 1;
            $rst = $data['data'] == 0 ? 0 : $this->calc($data['data']);
        }else{
            $code = 0;
            $rst = $data['data'];
        }
        return array(
            'code' => $code,
            'data' => $rst
        );
    }

    /*根据unspent_outputs 计算余额*/
    public function calc($unspent_outputs){
        $values = array_column($unspent_outputs,'value');

        $total = array_sum($values);
        return $total / 100000000;
    }

    /**
     * @param $num         科学计数法字符串  如 2.1E-5
     * @param int $double 小数点保留位数 默认18位
     * @return string
     */

    public function sctonum($num, $double = 18){
        if(false !== stripos($num, "e")){
            $a = explode("e",strtolower($num));
            $b = bcmul($a[0], bcpow(10, $a[1], $double), $double);
            $c = rtrim($b, '0');
            return $c;
        }else{
            return $num;
        }
    }
}