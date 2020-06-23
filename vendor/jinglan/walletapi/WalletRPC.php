<?php

namespace jinglan\walletapi;

class WalletRPC
{
    private $proto ;
    private $host ;
    private $port ;
    protected $_md5_key ;


    /**
     * 参数初始化
     * @param $method
     * @param $params
     */
    public function __construct($proto,$host,$port,$_md5_key){
        $this->proto = $proto;
        $this->host = $host;
        $this->port = $port;
        $this->_md5_key = $_md5_key;
    }

    //账户创建
    public function account_create($uid,$coin_symbol)
    {

      $params['coin_symbol'] = $coin_symbol;

      $params['uuid'] = $uid;
        
      $params= $this->filterPara($params);

      $params =  $this->buildRequestPara($params);

      $post_url = "{$this->proto}://{$this->host}:{$this->port}/api/wallet/account/create";


      $result = $this->curl_post($post_url,$params);

      $result_arr = json_decode($result,true);

      if($result_arr['code'] == 1){

          $ret['seed'] = $result_arr['data']['wallet_id'];

          $ret['memo'] = $result_arr['data']['memo'];

          $ret['address'] = $result_arr['data']['address'];  

          return array('code'=>1,'data'=>$ret);  

      }else{

          return array('code'=>0,'data'=>$result);            
      }

    }

    //账户信息
    public function account_info($wallet_id){

      $params['wallet_id'] = $wallet_id;
        
      $params= $this->filterPara($params);

      $params =  $this->buildRequestPara($params);

      $post_url = "{$this->proto}://{$this->host}:{$this->port}/api/wallet/account/info";

      $result = $this->curl_post($post_url,$params);

      $result_arr = json_decode($result,true);

      if($result_arr['code'] == 1){

          $ret['balance'] = $result_arr['data']['chain_balance'];  

          return array('code'=>1,'data'=>$ret);        
      }else{
          return array('code'=>0,'data'=>$result);            
      }

    }

    //账户转账

    public function account_transfer($wallet_id,$from_address,$amount,$to_address,$transaction_no){

      $params['wallet_id'] = $wallet_id;

      $params['address'] = $from_address;

      $params['amount'] = $amount;

      $params['to_address'] = $to_address;

      $params['transaction_no'] = $transaction_no;
        
      $params= $this->filterPara($params);

      $params =  $this->buildRequestPara($params);

      $post_url = "{$this->proto}://{$this->host}:{$this->port}/api/wallet/account/transfer";

      $result = $this->curl_post($post_url,$params);

      $result_arr = json_decode($result,true);

      if($result_arr['code'] == 1){
          
          $ret['tx_id'] = $result_arr['data']['transaction_id'];

          return array('code'=>1,'data'=>$ret);        
      }else{
          return array('code'=>0,'data'=>$result);            
      }

    }

    // curl for request
    private  function curl_post($url, $post_data = '', $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if ($post_data != '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }

        /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    public function paraFilter($para) {
        $para_filter = array();
        foreach ($para as $key => $val) {
            if($key == "sign" || $val == "")continue;
            else    $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    public function argSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    public function createLinkstring($para) {
        $arg  = "";
        foreach ($para as $key => $val) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,strlen($arg)-1);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){
            $arg = stripslashes($arg);
        }
        return $arg;
    }
    /**
     * 生成md5签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    public function md5Sign($prestr, $key) {
        $prestr = $prestr . $key;
        return md5($prestr);
    }

    public function filterPara($para_temp){
        $para_filter = $this->paraFilter($para_temp);//除去待签名参数数组中的空值和签名参数
        return $this->argSort($para_filter);//对待签名参数数组排序
    }
    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * @return string 签名结果字符串
     */
    public function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);
        $mysign = "";
        $mysign = $this->md5Sign($prestr, $this->_md5_key);

        return $mysign;
    }
    /**
     * 生成要发送的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    public function buildRequestPara($para_temp) {
        $para_sort = $this->filterPara($para_temp);//对待签名参数进行过滤
        $para_sort['sign'] = $this->buildRequestMysign($para_sort);//生成签名结果，并与签名方式加入请求提交参数组中
        return $para_sort;
    }
}
