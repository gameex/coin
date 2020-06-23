<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/6/4 0004
 * Time: 18:32
 */

namespace common\helpers;


class Bankcard{
    public function Luhn($no){
        $arr_no = str_split($no);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x == $last_n){
            return true;
        }else{
            return false;
        }
    }
}