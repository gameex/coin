<?php
namespace api\controllers;

use api\models\Coin;
use api\models\ExchangeRate;
use Yii;
use api\models\Member;
use common\controllers\ActiveController;
use common\models\base\AccessToken;
use api\models\MemberWallet;

class ApibaseController extends ActiveController{
	public $enableCsrfValidation = false;

	public function init(){
		parent::init();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
		header('Access-Control-Allow-Headers:x-requested-with, content-type');
		header("Content-type: text/html; charset=utf-8");
        $_POST['chain_network'] = 'main_network';
		//$this->update_exchange_rate();

		Yii::$app->set('mailer', [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => Yii::$app->config->info('MAILER_HOST'),
                'username' => Yii::$app->config->info('MAILER_USERNAME'),
                'password' => Yii::$app->config->info('MAILER_PASSWORD'),
                'port' => Yii::$app->config->info('MAILER_PORT'),
                'encryption' => empty(Yii::$app->config->info('MAILER_ENCRYPTION')) ? 'tls' : 'ssl',
            ],
            'messageConfig'=>[  
               'charset'=>'UTF-8',  
               'from'=>[Yii::$app->config->info('MAILER_FROM')=>Yii::$app->config->info('WEB_APP_NAME')]  
             ],
        ]);

	}

	//检测token
	public function checkToken($access_token){
		$this->check_empty($access_token,'_User_Information_Anomaly_');

        $access_token_info = AccessToken::findIdentityByAccessToken($access_token);
        if (empty($access_token_info)){
        	$actions = Yii::$app->params['user.optional'];
        	if (in_array($this->action->id, $actions)) {
        		return ['id'=>0];
        	}
            $this->do_aes(json_encode(['code'=>401,'message'=>'Unauthorized']));
        }
        $user_id = $access_token_info->attributes['user_id'];

        $uinfo = $this->getUserInfoById($user_id);
		$uinfo['access_token'] = $access_token;
        return $uinfo;
	}

	//检测PC Token
	public function memberToken($access_token){
		$this->check_empty($access_token,'_User_Information_Anomaly_');
        $access_token_info = Member::findAccessToken($access_token);
        if(empty($access_token_info)){
            $access_token_info = AccessToken::findIdentityByAccessToken($access_token);
        	if(empty($access_token_info)){
	        	// $this->error_message('token值输入错误');
	        	$actions = Yii::$app->params['user.optional'];
	        	if (in_array($this->action->id, $actions)) {
	        		return ['id'=>0];
	        	}
	        	$result = array('code'=>501, 'message'=>'Unauthorized', 'token_status'=>0);
	        	$this->do_aes(json_encode($result));
        	}else{
	    		$user_id = $access_token_info->attributes['user_id'];
	        	$uinfo = $this->getUserInfoById($user_id);
				$uinfo['access_token'] = $access_token;
	        	return $uinfo;
        	}
    	}else{
    		$user_id = $access_token_info->attributes['id'];
        	$uinfo = $this->getUserInfoById($user_id);
			$uinfo['access_token'] = $access_token;
        	return $uinfo;
    	}
	}

	//根据用户id获取用户信息
	protected function getUserInfoById($id){
		$this->check_empty($id,'_Error_Parameters_');
		$where['id']=$id;
		$select = 'id,username,password_hash,nickname,email,head_portrait,verified_status,mobile_phone,mobile_phone_status,status,otc_merchant,code,son_1_num,son_2_num,son_3_num,invite_rewards,invite_fee_rewards,total_invite_rewards,freeze_rewards,frozen_rewards,access_token';
		$result = Member::find()->select($select)->where($where)->asArray()->one();
		if($result){
			$result['head_portrait'] = $this->get_user_avatar_url($result["head_portrait"]);
			$result['exchange_password'] = empty($result['password_hash']) ? 0 : 1;
			unset($result['password_hash']);
            unset($result['status']);
			return $result;
		}elseif ($result['status'] != 10) {
			$this->error_message('_User_Information_Anomaly_');
		}else{
			$this->error_message('_User_Info_Get_Failed_Try_Again_Later_');
		}
	}

	//更新汇率
	protected function update_exchange_rate(){
		$coins = Coin::find()->where(['enable'=>1])->select('id,symbol,usd,cny,exchange_rate_updated_at')->asArray()->all();

		//查询汇率最后更新时间
		$exchange_rate_updated_at = array_column($coins,'exchange_rate_updated_at');
		sort($exchange_rate_updated_at);
		$earliest = $exchange_rate_updated_at[0];

		if(time() - $earliest > 1800){//30分钟更新一次汇率
			$symbols = array_column($coins,null, 'symbol');
			$symbol_key = array_keys($symbols);
			$fsym = join(',', $symbol_key);
			$tsyms = 'USD,CNY';
			$rst = $this->cryptocompare_api(strtoupper($fsym), $tsyms);

			if(!empty($rst)){
				foreach($coins as $v){
					$coin_model = Coin::findOne(['id'=>$v['id']]);
					$coin_model->usd = $rst[$v['symbol']]['USD'];
					$coin_model->cny = $rst[$v['symbol']]['CNY'];
					$coin_model->exchange_rate_updated_at = time();
					$coin_model->save();

					$exchange_rate_model = new ExchangeRate;
					$exchange_rate_model->coin_symbol = $v['symbol'];
					$exchange_rate_model->usd = $rst[$v['symbol']]['USD'];
					$exchange_rate_model->cny = $rst[$v['symbol']]['CNY'];
					$exchange_rate_model->save();
				}
			}
		}
	}

	private function cryptocompare_api($fsym,$tsyms){
		$url = "https://min-api.cryptocompare.com/data/pricemulti";
		$url = $url . '?fsyms=' . $fsym . '&tsyms=' . $tsyms;
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$rst = curl_exec($ch);
			curl_close($ch);
			if (false === $rst) {
				$rst = null;
			}
		}catch(Exception $e){
			$rst = null;
		}

		return json_decode($rst, true);
	}




	//参数不能为空
	protected function check_empty($input,$descrp='_NOT_Empty_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
		if (empty($input)) {
		    if(is_string($descrp)){
                $ret = array('code'=>500,'message'=>Yii::t($language,$descrp));
            }else{
                $ret = array('code'=>500,'message'=>$descrp);
            }
			$this->do_aes(json_encode($ret));
		}
	}

	//普通错误信息,客户端直接提示即可,客户端不需要对此状态吗做特殊处理
	protected function error_message($descrp='_Information_Wrong_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
        if(is_string($descrp)){
            $ret = array('code'=>501,'message'=>Yii::t($language,$descrp));
        }else{
            $ret = array('code'=>501,'message'=>$descrp);
        }
		$this->do_aes(json_encode($ret));
	}

	//普通成功信息,统一格式
	protected function success_message($data='',$descrp = '_Submission_Success_'){
        $language = Yii::$app->request->post('language') == 'en_us'?'en_us':'zh_cn';
		if (empty($data)) {
			$ret = array('code'=>200,'message'=>Yii::t($language,$descrp));
		}else{
			$ret = array('code'=>200,'data'=>$data,'message'=>Yii::t($language,$descrp));
		}
		$this->do_aes(json_encode($ret));
	}

	//统一返回处理
	protected function do_aes($str){
		die($str);
	}

	protected function undo_aes($str){
		die($str);
	}

	protected function get_user_avatar_url($avatar){
		if($avatar){
			if(strpos($avatar, "http")===0){
				return $avatar;
			}else{
				return \Yii::$app->request->hostInfo . $avatar;
			}
		}else{
			return \Yii::$app->request->hostInfo . '/attachment/images/head_portrait.png';
		}
	}

	protected function update_balance($coin_symbol, $coin_addr, $memo, $balance){
		$find_coin = MemberWallet::find()->where(['coin_symbol'=>$coin_symbol, 'addr'=>$coin_addr, 'memo'=>$memo])->one();
		if($find_coin){
			$wallet_model = MemberWallet::findOne(['coin_symbol'=>$coin_symbol, 'addr'=>$coin_addr, 'memo'=>$memo]);
			$wallet_model->balance = $balance;
			$wallet_model->save();
		}
	}
}