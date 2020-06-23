<?php
error_reporting(E_ERROR); 

ini_set("display_errors","Off");

class HuobiApi
{
    private $api       = 'api.huobi.pro';
    public $api_method = '';
    public $req_method = '';
    public $access;
    public $secret;
    public function __construct($access_key, $secret_key)
    {
    	$this->access = $access_key;
    	$this->secret = $secret_key;
        date_default_timezone_set("Etc/GMT+0");
    }
    /**
     * 行情类API
     */
    // 获取K线数据
    public function get_history_kline($symbol = '', $period = '', $size = 0)
    {
        $this->api_method = "/market/history/kline";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
            'period' => $period,
        ];
        if ($size) {
            $param['size'] = $size;
        }
        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url));
    }
    // 获取聚合行情(Ticker)
    public function get_detail_merged($symbol = '')
    {
        $this->api_method = "/market/detail/merged";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
        ];
        $url = $this->create_sign_url($param);

       // $data = '{"status":"ok","ch":"market.btcusdt.detail.merged","ts":1542185414679,"tick":{"amount":15447.192773568251,"open":6440.0,"close":6471.39,"high":6498.2,"id":28057280850,"count":113179,"low":6429.26,"version":28057280850,"ask":[6471.62,0.05261262946681047],"vol":9.984198874508053E7,"bid":[6471.38,1.4946]}}';
        
       // return json_decode($data);
        return json_decode($this->curl($url));
    }
    // 获取 Market Depth 数据
    public function get_market_depth($symbol = '', $type = '')
    {
        $this->api_method = "/market/depth";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
            'type'   => $type,
        ];
        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url));
    }
    // 获取 Trade Detail 数据
    public function get_market_trade($symbol = '')
    {
        $this->api_method = "/market/trade";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
        ];
        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url));
    }
    // 批量获取最近的交易记录
    public function get_history_trade($symbol = '', $size = '')
    {
        $this->api_method = "/market/history/trade";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
        ];
        if ($size) {
            $param['size'] = $size;
        }
        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url));
    }
    // 获取 Market Detail 24小时成交量数据
    public function get_market_detail($symbol = '')
    {
        $this->api_method = "/market/detail";
        $this->req_method = 'GET';
        $param            = [
            'symbol' => $symbol,
        ];
        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url));
    }
    /**
     * 公共类API
     */
    // 查询系统支持的所有交易对及精度
    public function get_common_symbols()
    {
        $this->api_method = '/v1/common/symbols';
        $this->req_method = 'GET';
        $url              = $this->create_sign_url([]);
        return json_decode($this->curl($url));
    }
    // 查询系统支持的所有币种
    public function get_common_currencys()
    {
        $this->api_method = "/v1/common/currencys";
        $this->req_method = 'GET';
        $url              = $this->create_sign_url([]);
        return json_decode($this->curl($url));
    }
    // 查询系统当前时间
    public function get_common_timestamp()
    {
        $this->api_method = "/v1/common/timestamp";
        $this->req_method = 'GET';
        $url              = $this->create_sign_url([]);
        return json_decode($this->curl($url));
    }
    // 查询当前用户的所有账户(即account-id)
    public function get_account_accounts()
    {
        $this->api_method = "/v1/account/accounts";
        $this->req_method = 'GET';
        $url              = $this->create_sign_url([]);
        return json_decode($this->curl($url));
    }
    // 查询指定账户的余额
    public function get_account_balance($account_id)
    {
        $this->api_method = "/v1/account/accounts/" . $account_id . "/balance";
        $this->req_method = 'GET';
        $url              = $this->create_sign_url([]);
        return json_decode($this->curl($url));
    }
    /**
     * 交易类API
     */
    // 下单
    public function place_order($account_id = 0, $amount = 0, $price = 0, $symbol = '', $type = '')
    {
        $source           = 'api';
        $this->api_method = "/v1/order/orders/place";
        $this->req_method = 'POST';
        // 数据参数
        $postdata = [
            'account-id' => $account_id,
            'amount'     => $amount,
            'source'     => $source,
            'symbol'     => $symbol,
            'type'       => $type,
        ];
        if ($price) {
            $postdata['price'] = $price;
        }
        $url    = $this->create_sign_url();
        $return = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 申请撤销一个订单请求
    public function cancel_order($order_id)
    {
        $source           = 'api';
        $this->api_method = '/v1/order/orders/' . $order_id . '/submitcancel';
        $this->req_method = 'POST';
        $postdata         = [];
        $url              = $this->create_sign_url();
        $return           = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 批量撤销订单
    public function cancel_orders($order_ids = [])
    {
        $source           = 'api';
        $this->api_method = '/v1/order/orders/batchcancel';
        $this->req_method = 'POST';
        $postdata         = [
            'order-ids' => $order_ids,
        ];
        $url    = $this->create_sign_url();
        $return = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 查询某个订单详情
    public function get_order($order_id)
    {
        $this->api_method = '/v1/order/orders/' . $order_id;
        $this->req_method = 'GET';
        $url              = $this->create_sign_url();
        $return           = $this->curl($url);
        return json_decode($return);
    }
    // 查询某个订单的成交明细
    public function get_order_matchresults($order_id = 0)
    {
        $this->api_method = '/v1/order/orders/' . $order_id . '/matchresults';
        $this->req_method = 'GET';
        $url              = $this->create_sign_url();
        $return           = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 查询当前委托、历史委托
    public function get_order_orders($symbol = '', $types = '', $start_date = '', $end_date = '', $states = '', $from = '', $direct = '', $size = '')
    {
        $this->api_method = '/v1/order/orders';
        $this->req_method = 'GET';
        $postdata         = [
            'symbol' => $symbol,
            'states' => $states,
        ];
        if ($types) {
            $postdata['types'] = $types;
        }
        if ($start_date) {
            $postdata['start-date'] = $start_date;
        }
        if ($end_date) {
            $postdata['end-date'] = $end_date;
        }
        if ($from) {
            $postdata['from'] = $from;
        }
        if ($direct) {
            $postdata['direct'] = $direct;
        }
        if ($size) {
            $postdata['size'] = $size;
        }
        $url    = $this->create_sign_url();
        $return = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 查询当前成交、历史成交
    public function get_orders_matchresults($symbol = '', $types = '', $start_date = '', $end_date = '', $from = '', $direct = '', $size = '')
    {
        $this->api_method = '/v1/order/matchresults';
        $this->req_method = 'GET';
        $postdata         = [
            'symbol' => $symbol,
        ];
        if ($types) {
            $postdata['types'] = $types;
        }
        if ($start_date) {
            $postdata['start-date'] = $start_date;
        }
        if ($end_date) {
            $postdata['end-date'] = $end_date;
        }
        if ($from) {
            $postdata['from'] = $from;
        }
        if ($direct) {
            $postdata['direct'] = $direct;
        }
        if ($size) {
            $postdata['size'] = $size;
        }
        $url    = $this->create_sign_url();
        $return = $this->curl($url, $postdata);
        return json_decode($return);
    }
    // 获取账户余额
    public function get_balance($account_id = ACCOUNT_ID)
    {
        $this->api_method = "/v1/account/accounts/{$account_id}/balance";
        $this->req_method = 'GET';
        $url              = $this->create_sign_url();
        $return           = $this->curl($url);
        $result           = json_decode($return);
        return $result;
    }
    /**
     * 借贷类API
     */
    // 现货账户划入至借贷账户
    public function dw_transfer_in($symbol = '', $currency = '', $amount = '')
    {
        $this->api_method = "/v1/dw/transfer-in/margin";
        $this->req_method = 'POST';
        $postdata         = [
            'symbol	'  => $symbol,
            'currency' => $currency,
            'amount'   => $amount,
        ];
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 借贷账户划出至现货账户
    public function dw_transfer_out($symbol = '', $currency = '', $amount = '')
    {
        $this->api_method = "/v1/dw/transfer-out/margin";
        $this->req_method = 'POST';
        $postdata         = [
            'symbol	'  => $symbol,
            'currency' => $currency,
            'amount'   => $amount,
        ];
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 申请借贷
    public function margin_orders($symbol = '', $currency = '', $amount = '')
    {
        $this->api_method = "/v1/margin/orders";
        $this->req_method = 'POST';
        $postdata         = [
            'symbol	'  => $symbol,
            'currency' => $currency,
            'amount'   => $amount,
        ];
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 归还借贷
    public function repay_margin_orders($order_id = '', $amount = '')
    {
        $this->api_method = "/v1/margin/orders/{$order_id}/repay";
        $this->req_method = 'POST';
        $postdata         = [
            'amount' => $amount,
        ];
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 借贷订单
    public function get_loan_orders($symbol = '', $currency = '', $start_date, $end_date, $states, $from, $direct, $size)
    {
        $this->api_method = "/v1/margin/loan-orders";
        $this->req_method = 'GET';
        $postdata         = [
            'symbol'   => $symbol,
            'currency' => $currency,
            'states'   => $states,
        ];
        if ($currency) {
            $postdata['currency'] = $currency;
        }
        if ($start_date) {
            $postdata['start-date'] = $start_date;
        }
        if ($end_date) {
            $postdata['end-date'] = $end_date;
        }
        if ($from) {
            $postdata['from'] = $from;
        }
        if ($direct) {
            $postdata['direct'] = $direct;
        }
        if ($size) {
            $postdata['size'] = $size;
        }
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 借贷账户详情
    public function margin_balance($symbol = '')
    {
        $this->api_method = "/v1/margin/accounts/balance";
        $this->req_method = 'POST';
        $postdata         = [
        ];
        if ($symbol) {
            $postdata['symbol'] = $symbol;
        }
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    /**
     * 虚拟币提现API
     */
    // 申请提现虚拟币
    public function withdraw_create($address = '', $amount = '', $currency = '', $fee = '', $addr_tag = '')
    {
        $this->api_method = "/v1/dw/withdraw/api/create";
        $this->req_method = 'POST';
        $postdata         = [
            'address'  => $address,
            'amount'   => $amount,
            'currency' => $currency,
        ];
        if ($fee) {
            $postdata['fee'] = $fee;
        }
        if ($addr_tag) {
            $postdata['addr-tag'] = $addr_tag;
        }
        $url    = $this->create_sign_url($postdata);
        $return = $this->curl($url);
        $result = json_decode($return);
        return $result;
    }
    // 申请取消提现虚拟币
    public function withdraw_cancel($withdraw_id = '')
    {
        $this->api_method = "/v1/dw/withdraw-virtual/{$withdraw_id}/cancel";
        $this->req_method = 'POST';
        $url              = $this->create_sign_url();
        $return           = $this->curl($url);
        $result           = json_decode($return);
        return $result;
    }
    /**
     * 类库方法
     */
    // 生成验签URL
    public function create_sign_url($append_param = [])
    {
        // 验签参数
        $param = [
            'AccessKeyId'      => $this->access,
            'SignatureMethod'  => 'HmacSHA256',
            'SignatureVersion' => 2,
            'Timestamp'        => date('Y-m-d\TH:i:s', time()),
        ];
        if ($append_param) {
            foreach ($append_param as $k => $ap) {
                $param[$k] = $ap;
            }
        }
        return 'https://' . $this->api . $this->api_method . '?' . $this->bind_param($param);
    }
    // 组合参数
    public function bind_param($param)
    {
        $u         = [];
        $sort_rank = [];
        foreach ($param as $k => $v) {
            $u[]         = $k . "=" . urlencode($v);
            $sort_rank[] = ord($k);
        }
        asort($u);
        $u[] = "Signature=" . urlencode($this->create_sig($u));
        return implode('&', $u);
    }
    // 生成签名
    public function create_sig($param)
    {
        $sign_param_1 = $this->req_method . "\n" . $this->api . "\n" . $this->api_method . "\n" . implode('&', $param);
        $signature    = hash_hmac('sha256', $sign_param_1, $this->secret, true);
        return base64_encode($signature);
    }
    public function curl($url, $postdata = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($this->req_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        return $output;
    }

}


$_HUOBI_API = new HuobiApi("f006fe4c-84e6b5da-d46d867e-58d04","d2d72bd6-a3115666-ace0993f-0c396");

$ret = $_HUOBI_API->get_detail_merged("btcusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['btcusdt']->max_price = $ask_price;
    $data['btcusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("ethusdt");

//获取成功
if($ret->status == 'ok'){
    $ask_price = $ret->tick->ask[0]; //最新卖单
    $bid_price = $ret->tick->bid[0]; //最新买单  
    //var_dump($ask_price);  
    //var_dump($bid_price);
    //$max_price =  $ask_price ;
    //$min_price =  $bid_price ;
    $data['ethusdt']->max_price = $ask_price;
    $data['ethusdt']->min_price  = $bid_price;
}

$ret = $_HUOBI_API->get_detail_merged("ethbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['ethbtc']->max_price = $ask_price;
    $data['ethbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("xrpusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['xrpusdt']->max_price = $ask_price;
    $data['xrpusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("ltcusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['ltcusdt']->max_price = $ask_price;
    $data['ltcusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("etcusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['etcusdt']->max_price = $ask_price;
    $data['etcusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("eosusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['eosusdt']->max_price = $ask_price;
    $data['eosusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("trxusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['trxusdt']->max_price = $ask_price;
    $data['trxusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("bttusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['bttusdt']->max_price = $ask_price;
    $data['bttusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("neousdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['neousdt']->max_price = $ask_price;
    $data['neousdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("mkrusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['mkrusdt']->max_price = $ask_price;
    $data['mkrusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("cmtusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['cmtusdt']->max_price = $ask_price;
    $data['cmtusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("omgusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['omgusdt']->max_price = $ask_price;
    $data['omgusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("batusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['batusdt']->max_price = $ask_price;
    $data['batusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("linkusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['linkusdt']->max_price = $ask_price;
    $data['linkusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("sntusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['sntusdt']->max_price = $ask_price;
    $data['sntusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("zilusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['zilusdt']->max_price = $ask_price;
    $data['zilusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("xrpbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['xrpbtc']->max_price = $ask_price;
    $data['xrpbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("zilbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['zilbtc']->max_price = $ask_price;
    $data['zilbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("eosbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['eosbtc']->max_price = $ask_price;
    $data['eosbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("ltcbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['ltcbtc']->max_price = $ask_price;
    $data['ltcbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("trxbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['trxbtc']->max_price = $ask_price;
    $data['trxbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("neobtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['neobtc']->max_price = $ask_price;
    $data['neobtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("mkrbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['mkrbtc']->max_price = $ask_price;
    $data['mkrbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("etcbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['etcbtc']->max_price = $ask_price;
    $data['etcbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("bttbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['bttbtc']->max_price = $ask_price;
    $data['bttbtc']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("btteth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['btteth']->max_price = $ask_price;
    $data['btteth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("eoseth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['eoseth']->max_price = $ask_price;
    $data['eoseth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("omgeth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['omgeth']->max_price = $ask_price;
    $data['omgeth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("leteth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['leteth']->max_price = $ask_price;
    $data['leteth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("ocneth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['ocneth']->max_price = $ask_price;
    $data['ocneth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("abteth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['abteth']->max_price = $ask_price;
    $data['abteth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("zileth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['zileth']->max_price = $ask_price;
    $data['zileth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("cmteth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['cmteth']->max_price = $ask_price;
    $data['cmteth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("zileth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['zileth']->max_price = $ask_price;
    $data['zileth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("elfeth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['elfeth']->max_price = $ask_price;
    $data['elfeth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("elfusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['elfusdt']->max_price = $ask_price;
    $data['elfusdt']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("htusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['htusdt']->max_price = $ask_price;
    $data['htusdt']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("elfbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['elfbtc']->max_price = $ask_price;
    $data['elfbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("cmtbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['cmtbtc']->max_price = $ask_price;
    $data['cntbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("htbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['htbtc']->max_price = $ask_price;
    $data['htbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("zilbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['zilbtc']->max_price = $ask_price;
    $data['zilbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("linkbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['linkbtc']->max_price = $ask_price;
    $data['linkbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("xrpbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['xrpbtc']->max_price = $ask_price;
    $data['xrpbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("xrpeth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['xrpeth']->max_price = $ask_price;
    $data['xrpeth']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("linketh");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['linketh']->max_price = $ask_price;
    $data['linketh']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("ltceth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['ltceth']->max_price = $ask_price;
    $data['ltceth']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("hteth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['hteth']->max_price = $ask_price;
    $data['hteth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("bixusdt");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['bixusdt']->max_price = $ask_price;
    $data['bixusdt']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("bixbtc");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['bixbtc']->max_price = $ask_price;
    $data['bixbtc']->min_price  = $bid_price;
     

}
$ret = $_HUOBI_API->get_detail_merged("bixeth");

//获取成功
if($ret->status == 'ok'){

    $ask_price = $ret->tick->ask[0]; //最新卖单

    $bid_price = $ret->tick->bid[0]; //最新买单
    
    //var_dump($ask_price);
    
    //var_dump($bid_price);

    //$max_price =  $ask_price ;

    //$min_price =  $bid_price ;
    $data['bixeth']->max_price = $ask_price;
    $data['bixeth']->min_price  = $bid_price;
     

}

$ret = $_HUOBI_API->get_detail_merged("adausdt");
//获取成功
if($ret->status == 'ok'){
    $ask_price = $ret->tick->ask[0]; //最新卖单
    $bid_price = $ret->tick->bid[0]; //最新买单
    $data['adausdt']->max_price = $ask_price;
    $data['adausdt']->min_price  = $bid_price;
}

$ret = $_HUOBI_API->get_detail_merged("dashusdt");
//获取成功
if($ret->status == 'ok'){
    $ask_price = $ret->tick->ask[0]; //最新卖单
    $bid_price = $ret->tick->bid[0]; //最新买单
    $data['dashusdt']->max_price = $ask_price;
    $data['dashusdt']->min_price  = $bid_price;
}

$ret = $_HUOBI_API->get_detail_merged("xmrusdt");
//获取成功
if($ret->status == 'ok'){
    $ask_price = $ret->tick->ask[0]; //最新卖单
    $bid_price = $ret->tick->bid[0]; //最新买单
    $data['xmrusdt']->max_price = $ask_price;
    $data['xmrusdt']->min_price  = $bid_price;
}

$ret = $_HUOBI_API->get_detail_merged("bchusdt");
//获取成功
if($ret->status == 'ok'){
    $ask_price = $ret->tick->ask[0]; //最新卖单
    $bid_price = $ret->tick->bid[0]; //最新买单
    $data['bchusdt']->max_price = $ask_price;
    $data['bchusdt']->min_price  = $bid_price;
}
die(json_encode($data)); 