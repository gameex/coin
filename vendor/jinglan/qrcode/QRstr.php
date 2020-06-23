<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 14:11
 */

namespace jinglan\qrcode;


class QRstr
{
    public static function set(&$srctab, $x, $y, $repl, $replLen = false) {
        $srctab[$y] = substr_replace($srctab[$y], ($replLen !== false)?substr($repl,0,$replLen):$repl, $x, ($replLen !== false)?$replLen:strlen($repl));
    }
}