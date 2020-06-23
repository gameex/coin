<?php 
namespace common\jinglan;

use yii\helpers\Url;
use jinglan\ethereum\EthereumRPC;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;

class TransferAccountsHelper
{
	// 返回数据
	public static function make_data($status=false, $msg='',$data='')
	{
		$result = ['status'=>$status, 'message'=>$msg];
		if (!empty($data)) {
			$result['data'] = $data;
		}
		return $result;
	}
	// eth转账【$gas, $gasPrice均为16进制,$value为十进制】
	public static function eth($from, $to, $value, $password, $gas='', $gasPrice='')
	{
		// （1）解锁银行账户地址
        $map1    = 'personal_unlockAccount';
        $map2    = [$from, (string)$password, 3];
        $rpc     = new EthereumRPC($map1, $map2);
        $rpc_ret = $rpc->do_rpc();
        if ($rpc_ret['code'] == 0) {
        	return self::make_data(false, '解锁失败：'.$rpc_ret['data']);
        }
		// （2）发起交易
		$method = 'eth_sendTransaction';
        $parameters = [
			'from'     => $from,
			'to'       => $to,
            // 大数值十进制转十六进制
			'value'    => '0x'.$rpc->bc_dechex((float)$value*pow(10, 18) - 21000*20*pow(10, 9)),
        ];
        if (!empty($gas)  && !empty($gasPrice)) {
			$parameters['gas']      = $gas;
			$parameters['gasPrice'] = $gasPrice;
        }else{
            $parameters['gas']      = '0x5208';
            $parameters['gasPrice'] = '0x'.$rpc->bc_dechex(10*pow(10, 9));// 默认支付gasprice为10Gwei
        }
        $rpc     = new EthereumRPC($method, [$parameters]);
        $rpc_ret = $rpc->do_rpc();
        if ($rpc_ret['code'] == 0) {
            return self::make_data(false, '交易过程失败：'.$rpc_ret['data']);
        }else{
            $tx_hash = $rpc_ret['data'];
        }

		// （3）钱包上锁
        $map1         = 'personal_lockAccount';
        $map2         = $from;
        $rpc          = new EthereumRPC($map1, $map2);
        $rpc_ret_lock = $rpc->do_rpc();
        $tx_hash = \yii\helpers\Html::a($tx_hash,['main/redirect','url'=>'https://etherscan.io/tx/'.$tx_hash], ['target'=>'_blank']);
        return self::make_data(true, '', $tx_hash);
	}

	// btc转账
	public static function btc($accountName, $to, $value)
	{
		// 开始执行转出操作【所需参数1：发起方账户名称，2：接收方地址，3：交易金额(十进制)】
        $map1       = 'sendfrom';
        $map2       = [(string)$accountName, $to, (double)$value];
        $btn_client = new BitcoinClient();
        $btc_result = $btn_client->request($map1, $map2);
        if ($btc_result['code'] == 0) {
        	return self::make_data(false, $btc_result['data']);
        }else{
            $tx_hash = $btc_result['data']->get();
        }
        return self::make_data(true, '', $tx_hash);
	}

	// usdt转账
	public static function usdt($accountName, $to, $value)
	{
		// 开始执行转出操作【所需参数1：发起方账户名称，2：接收方地址，3：交易金额(十进制)】
        $map1        = 'sendfrom';
        $map2        = [(string)$accountName, $to, (double)$value];
        $usdt_client = new OmnicoreClient();
        $usdt_result = $usdt_client->request($map1, $map2);
        if ($usdt_result['code'] == 0) {
        	return self::make_data(false, $usdt_result['data']);
        }else{
            $tx_hash = $usdt_result['data']->get();
        }
        return self::make_data(true, '', $tx_hash);
	}

	// 代币转账【$to为合约地址，且$gas, $gasPrice均为16进制，$value为十进制】
	public static function token($from, $to, $value, $password, $contractAddress,$ram_token_decimals, $gas='', $gasPrice='')
	{
        //00.先查地址是否有ETH作为矿工费
        $rpc = new EthereumRPC('eth_getBalance', [$from,'latest']);
        $rpc_ret = $rpc->do_rpc();
        if ($rpc_ret['code'] == 0) {
            return self::make_data(false, '前期查ETH余额失败：'.$rpc_ret['data']);
        }
        $eth_balance = hexdec($rpc_ret['data']) / pow(10, 18);
        $ram_gas = (int)Jinglan::get_extension('RAM_gas');
        $ram_gas_price = (string)Jinglan::get_extension("ETH_gasPrice");
        $ram_fee = $eth_balance - ($ram_gas * (hexdec($ram_gas_price) / pow(10,18)));
        if ($ram_fee < 0){
            return self::make_data(false, '该地址ETH余额不足支付矿工费');
        }
//        //01.查代币余额
//        $map2 = ['to'=>$contractAddress, 'data'=>"0x70a08231000000000000000000000000" .substr($from, 2)];
//        $rpc = new EthereumRPC('eth_call', [$map2,"latest"]);
//        $rpc_ret = $rpc->do_rpc();
//        if ($rpc_ret['code'] == 0) {
//            return self::make_data(false, '前期查代币余额失败：'.$rpc_ret['data']);
//        }
//        $ram_balance = hexdec($rpc_ret['data']) / pow(10, $ram_token_decimals);
//        $value = $ram_balance;
        //02.构造交易数据
        // 大数值十进制转十六进制
        $value      = '0x'.$rpc->bc_dechex((int)$value*pow(10, $ram_token_decimals));
        $format_str = substr($value, 2);
        for ($i=0; $i < (64+2-strlen($value)); $i++) {
            $format_str = '0'.$format_str;
        }
        $data   = '0xa9059cbb000000000000000000000000'.substr($to, 2).$format_str;
        $parameters = [
            'from'  => $from,
            'to'    => $contractAddress,
            // 'value' => $value,
            'data'  => $data,
        ];
        if (!empty($gas)  && !empty($gasPrice)) {
            $parameters['gas']      = $gas;
            $parameters['gasPrice'] = $gasPrice;
        }else{
//            $parameters['gas']      = '0x33450';//32000=>7d00,210000=>33450
//            $parameters['gasPrice'] = '0x'.$rpc->bc_dechex(10*pow(10, 9));// 默认支付gasprice为10Gwei
            $parameters['gas']      = '0x'.$rpc->bc_dechex($ram_gas);
            $parameters['gasPrice'] = $ram_gas_price;
        }//p($parameters);exit();
        // （1）解锁账户地址
        $map1    = 'personal_unlockAccount';
        $map2    = [$from, (string)$password, 1];
        $rpc     = new EthereumRPC($map1, $map2);
        $rpc_ret = $rpc->do_rpc();
        if ($rpc_ret['code'] == 0) {
            return self::make_data(false, '解锁失败：'.$rpc_ret['data']);
        }
        // （2）发起交易
        $rpc     = new EthereumRPC('eth_sendTransaction', [$parameters]);
        $rpc_ret = $rpc->do_rpc();
        if ($rpc_ret['code'] == 0) {
            return self::make_data(false, '交易过程失败：'.$rpc_ret['data']);
        }else{
            $tx_hash = $rpc_ret['data'];
        }

        // （3）钱包上锁
        $map1         = 'personal_lockAccount';
        $map2         = $from;
        $rpc          = new EthereumRPC($map1, $map2);
        $rpc_ret_lock = $rpc->do_rpc();
        $tx_hash = \yii\helpers\Html::a($tx_hash,['main/redirect','url'=>'https://etherscan.io/tx/'.$tx_hash], ['target'=>'_blank']);
        return self::make_data(true, '', $tx_hash);
	}
}