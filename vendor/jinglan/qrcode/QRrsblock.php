<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 13:07
 */

namespace jinglan\qrcode;


class QRrsblock {
    public $dataLength;
    public $data = array();
    public $eccLength;
    public $ecc = array();

    public function __construct($dl, $data, $el, &$ecc, QRrsItem $rs)
    {
        $rs->encode_rs_char($data, $ecc);

        $this->dataLength = $dl;
        $this->data = $data;
        $this->eccLength = $el;
        $this->ecc = $ecc;
    }
};