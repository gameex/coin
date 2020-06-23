<?php
namespace api\controllers;

use api\models\IpLog;
use api\models\Member;
use api\models\Varcode;
use common\jinglan\Jinglan;
use common\models\base\AccessToken;
use api\models\Coin;
use api\models\Message;
use api\models\MemberContact;
use api\models\MemberWallet;
use Yii;
use yii\db\Expression;
use yii\web\UploadedFile;
use common\helpers\FileHelper;
use common\helpers\StringHelper;
use jinglan\sms\SMS;
use Denpa\Bitcoin\Client as BitcoinClient;
use api\models\MemberVerified;
use common\jinglan\CreateWallet;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use common\models\OtcMerchants;

class MemberController extends ApibaseController{
	public $modelClass = '';

	public function init(){
		parent::init();
	}

    //C-0.创建用户
    public function actionLogin(){
        $request = Yii::$app->request;
        $message = $request->post('message');
        $this->check_empty($message,'_Message_Not_Empty_');
        if(strlen($message) != 21){
            $this->error_message('_Unlawful_Request_');
        }

        $nickname = StringHelper::random(8);
        $users_model= new Member();
        $users_model->nickname = $nickname;
        $users_model->head_portrait = '/attachment/images/head_portrait.png';
		$users_model->visit_count = 1;
		$users_model->last_time = time();
		$users_model->last_ip = Yii::$app->request->getUserIP();
        if($users_model->save() > 0){
            $user_id = $users_model->attributes['id'];
            $group = 1;
            $rst = AccessToken::setMemberInfo($group, $user_id);

            $uinfo = $this->getUserInfoById($user_id);
            $uinfo['access_token'] = $rst['access_token'];
            $this->success_message($uinfo);
        }else{
            $this->error_message('_Registration_Failed_Try_Later_');
        }
    }



	//获取用户信息
	public function actionInfo(){
		$request = Yii::$app->request;
		$language = $request->post('language');
        $language =  $language == 'en_us' ? 'en_us' : 'zh_cn';
		$select = $language == 'en_us' ? 'usd' : 'cny';

		$access_token = $request->post('access_token');

		$uinfo = $this->checkToken($access_token);
		//获取汇率
		$rate = Coin::find()->where(['symbol'=>'BTC'])->select('usd,cny')->asArray()->one();
		if($language == 'en_us'){
			$uinfo['rate'] = '1 BTC = ' . '$' . $rate[$select];
		}else{
			$uinfo['rate'] = '1 BTC = ' . '￥' . $rate[$select];
		}
		//查询认证状态
		$verified_data = MemberVerified::find()->where(['uid' => $uinfo['id']])->andWhere(['>', 'status', '0'])->asArray()->one();
		if(empty($verified_data)){
			$verified_status = 0;
		}else{
			switch($verified_data['status']){
				case 0: 
					$verified_status = 0;//未认证
					break;
				case 1:
					$verified_status = 1;//未审核
					break;
				case 2:
					$verified_status = 2;//审核通过
					break;
				case 3:
					$verified_status = 3;//审核未通过
					break;
			}
		}
        $verified_status_msg = [
                Yii::t($language,'_No_Certification_Certified_In_Time_'),
                Yii::t($language,'_Waiting_For_Audit_'),
                Yii::t($language,'_Certified_'),
                Yii::t($language,'_Audit_Failed_Upload_Real_Info_'),
            ];
        /***********************商家用户认证状态信息【新增】开始***********************/
        $otc_merchants = OtcMerchants::find()->select(['status'])->where(['uid'=>intval($uinfo['id'])])->andWhere(['<>','status',0])->asArray()->one();
        $otc_merchant_msg = [
    		Yii::t($language,'_No_Certification_Certified_In_Time_'),//未认证
            Yii::t($language,'_Waiting_For_Audit_'),//等待审核
            Yii::t($language,'_Audit_Has_Passed_'),//审核通过
            Yii::t($language,'_Audit_Failed_Upload_Real_Info_'),//审核未通过
    	];
        if ($otc_merchants) {
    		$uinfo['otc_merchant_msg'] = $otc_merchant_msg[$otc_merchants['status']];
    		$uinfo['otc_merchant'] = $otc_merchants['status'];
        }else{
        	$uinfo['otc_merchant_msg'] = $otc_merchant_msg[0];
        	$uinfo['otc_merchant'] = "0";
        }
        /***********************商家用户认证状态信息【新增】结束***********************/
		$uinfo['verified_status'] = $verified_status;
		$uinfo['verified_status_msg'] = $verified_status_msg[$verified_status];
        $uinfo['usd_to_cny'] = Jinglan::usd_to_cny();

        /**************开始创建用户钱包***************/
        // 判断用户是否设置交易所密码【设置则表明已经绑定手机、实名认证】
        if ($uinfo['exchange_password'] != 1) {
        	//$this->success_message($uinfo);
        }
        //CreateWallet::create($uinfo['id']);
        
        /**************结束创建用户钱包***************/
		$this->success_message($uinfo);
	}

	//获取币种列表
	public function actionCoinList(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);
		$list = MemberWallet::find()->where(['uid'=>$uinfo['id']])->select('coin_symbol')->asArray()->all();
		if(empty($list)){
			$this->error_message('_No_Data_Query_');
		}else{
			foreach($list as $key => &$val) {
				if (isset($_POST['chain_network']) && $_POST['chain_network'] == 'main_network' && $val['coin_symbol'] == 'TOKEN KKCC') {
					unset($list[$key]);
					continue;
				}
				if (isset($_POST['chain_network']) && $_POST['chain_network'] != 'main_network' && $val['coin_symbol'] == 'UVC Token') {
					unset($list[$key]);
					continue;
				}
			}
		}
		//$where['enable']=1;
		$select = 'symbol,coin_name,icon';
		$result = Coin::find()->select($select)->where(['in','symbol',array_column($list,'coin_symbol')])->groupBy('symbol')->orderBy('usd DESC')->asArray()->all();
		if ($result) {
			foreach($result as &$v){
				$v['icon'] = parent::get_user_avatar_url($v['icon']);
			}
			$this->success_message($result,'_Success_');
		}else{
			$this->error_message('_No_Data_Query_');
		}
	}

	//获取消息列表
	public function actionMessageList(){
		$request = Yii::$app->request;
		$access_token = $request->post('access_token');
        $os = strtolower($request->post('os'));
        if (!empty($access_token)){
            if (in_array($os, ['ios','android'])){
                $uinfo = $this->checkToken($access_token);
            }else{
                $uinfo = $this->memberToken($access_token);
            }
            $uid = $uinfo['id'];
        }else{
            $uid = 0;
        }
		$type = intval($request->post('type'));
		if ($type < 0) {
			$this->error_message('_MessageType_Wrong_');
		}
		$access_token = $request->post('access_token');

		$where['status']=1;
		$where['type']=$type;
		if ($type == 1) {
            $uinfo = $this->checkToken($access_token);
			$where['uid'] = $uinfo['id'];
		}
		$result = Message::find()
				->select(new Expression("title,content,from_unixtime(add_time,'%Y-%m-%d %H:%m') add_time"))
				->where($where)->orderBy("id desc")->asArray()->all();
		if ($result) {
			$this->success_message($result,'_Success_');
		}else{
			$this->error_message('_No_Data_Query_');
		}
	}

	//添加联系人
	public function actionAddContact(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);

		$name = $request->post('name');
		$wallet_addr = $request->post('wallet_addr');
		$coin_symbol = $request->post('coin_symbol');
		$mobile = $request->post('mobile');
		$email = $request->post('email');
		$remark = $request->post('remark');

		$this->check_empty($name,'_Name_NOT_Empty_');
		$this->check_empty($wallet_addr,'_Wallet_Address_Not_Empty_');
		$this->check_empty($coin_symbol,'_Address_Type_Not_Empty_');

		$coins = Coin::find()->select("id,symbol")->where(['symbol'=>$coin_symbol])->asArray()->all();
		if(empty($coins)){
			$this->error_message('_MoneyType_Wrong_');
		}

		$result = MemberContact::find()->select('id')->where(['uid'=>$uinfo['id'], 'coin_symbol'=>$coin_symbol, 'wallet_addr'=>$wallet_addr])->asArray()->one();
		if($result){
			$this->error_message('_Not_Repeat_Add_');
		}else{
			$contact_model = new MemberContact;
			$contact_model->uid = $uinfo['id'];
			$contact_model->name = $name;
			$contact_model->coin_symbol = $coin_symbol;
			$contact_model->wallet_addr = $wallet_addr;
			$contact_model->mobile = $mobile;
			$contact_model->email = $email;
			$contact_model->remark = $remark;

			if($contact_model->save() > 0){
                $this->success_message();
			}else{
                $this->error_message('_Add_Failure_');
            }
		}
	}


	//获取联系人列表
	public function actionContactList(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);

		$select = 'id,name,coin_symbol,wallet_addr,mobile,email,remark';
		$result = MemberContact::find()->select($select)->where(['uid'=>$uinfo['id']])->asArray()->all();
		if ($result) {
			$this->success_message($result,'_Success_');
		}else{
			$this->error_message('_No_Data_Query_');
		}
	}

	//修改联系人资料
	public function actionModifyContact(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);

		$id = intval($request->post('id'));
		$name = $request->post('name');
		$wallet_addr = $request->post('wallet_addr');
		$coin_symbol = $request->post('coin_symbol');
		$mobile = $request->post('mobile');
		$email = $request->post('email');
		$remark = $request->post('remark');

		$this->check_empty($name,'_Name_NOT_Empty_');
		$this->check_empty($wallet_addr,'_Wallet_Address_Not_Empty_');
		$this->check_empty($coin_symbol,'_Address_Type_Not_Empty_');

		$where['id'] = $id;
		$where['uid'] = $uinfo['id'];
		//$contact_model = MemberContact::find()->where($where)->createCommand()->getRawSql();
		$contact_model = MemberContact::find()->where($where)->one();
		//var_dump($contact_model);
		if ($contact_model) {
			$contact_model->name = $name;
			$contact_model->coin_symbol = $coin_symbol;
			$contact_model->wallet_addr = $wallet_addr;
			$contact_model->mobile = $mobile;
			$contact_model->email = $email;
			$contact_model->remark = $remark;
			$contact_model->save();
			$this->success_message('','_Update_success_');
		}else{
			$this->error_message('_Operation_Wrong_Try_Again_');
		}

	}

	//删除联系人
	public function actionDeleteContact(){
		$request = Yii::$app->request;
		$access_token = $request->post('access_token');
		$uinfo = $this->checkToken($access_token);
		$id = intval($request->post('id'));

		$where['id'] = $id;
		$where['uid'] = $uinfo['id'];
		$contact_model = MemberContact::find()->where($where)->one();
		if ($contact_model) {
			$contact_model->delete();
			$this->success_message('','_Delete_Success_');
		}else{
			$this->error_message('_Operation_Wrong_Try_Again_');
		}


	}

	//C-4.个人中心验证手机号
	public function actionGetMobileVarcode(){
		$request = Yii::$app->request;

		$access_token = $request->post('access_token');
		$mobile_phone = $request->post('mobile_phone');
		$this->check_empty($mobile_phone,'_PhoneNum_Not_Empty_');
		if(!preg_match("/^1[3|4|5|6|7|8][0-9]\d{4,8}$/",$mobile_phone)){
                $this->error_message('_MobilePhoneNum_Illegal_');
		}

		$uinfo = $this->checkToken($access_token);

		//短信服务检验
		$limit_time = Yii::$app->config->info("SMS_SEND_LIMIT_TIME") > 0 ? intval(Yii::$app->config->info("SMS_SEND_LIMIT_TIME")) : 5;

		$varcode_result = Varcode::find()->where(['mobile_phone'=>$mobile_phone])->one();
		if($varcode_result){
			if(time() - $varcode_result->attributes['updated_at'] < $limit_time*60){
				$this->error_message($limit_time.'_Within_Minute_One_');
			}
		}

        $ip = Yii::$app->request->getUserIP();
        if(Yii::$app->config->info("SMS_IP_LIMIT_ENABLE") == 1){
            $limit_times = Yii::$app->config->info("SMS_IP_LIMIT_TIMES") > 0 ? intval(Yii::$app->config->info("SMS_IP_LIMIT_TIMES")) : 5;
            $varcode_result2 = Varcode::find()->where(['ip'=>$ip])->one();
            if($varcode_result2){
                if(time() - $varcode_result2->attributes['updated_at'] < $limit_time*60){
                    $this->error_message($limit_time.'_Within_Minute_Two_');
                }
            }

            //记录请求ip，限制同一ip一天内请求次数
            //查询是否有该ip
            $ip_result = IpLog::find()->where(['ip'=>$ip])->one();
            if(empty($ip_result)){
                $ip_log_model = new IpLog();
                $ip_log_model->ip = $ip;
                $ip_log_model->times = 1;
                $ip_log_model->save();
            }else{
                $ip_log_model = IpLog::findOne(['id'=>$ip_result->attributes['id']]);
                if(time() - $ip_result->attributes['updated_at'] > 86400){//本次请求大于上次1天
                    $ip_log_model->ip = $ip;
                    $ip_log_model->times = 1;
                }else{//同一天的请求
                    if($ip_result->attributes['times'] >= $limit_times){
                        $this->error_message('_Exceed_Set_Network_Num_');
                    }else{
                        $ip_log_model->times += 1;
                    }
                }
                $ip_log_model->save();
            }
        }

        $code = strval(rand(100000,999999));
        $sms = new SMS($mobile_phone, $code);
		$send_result = $sms->send();
		if($send_result['code'] == 0) {//发送成功
			if(empty($varcode_result)) {//表中无记录
				$varcode_model = new Varcode();
				$varcode_model->mobile_phone = $mobile_phone;
				$varcode_model->varcode = $code;
				$varcode_model->ip = $ip;
				$varcode_model->member_id = $uinfo['id'];
				if($varcode_model->save() > 0){
					$this->success_message();
				}else{
					$this->error_message('SAVE ERROR#1');
				}
			}else {//表中有记录
                $varcode_model = Varcode::findOne(['id'=>$varcode_result->id]);
				$varcode_model->mobile_phone = $mobile_phone;
				$varcode_model->varcode = $code;
				$varcode_model->ip = $ip;
				$varcode_model->member_id = $uinfo['id'];
				if($varcode_model->save() > 0){
					$this->success_message();
				}else{
					$this->error_message('SAVE ERROR#2');
				}
			}
		}else{
			$this->error_message($send_result['message']);
		}
	}

    //C-4-2.个人中心绑定手机号
    public function actionBindMobileVarcode()
    {
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $mobile_phone = $request->post('mobile_phone');
        $varcode = $request->post('varcode');
        $this->check_empty($mobile_phone, '_PhoneNum_Not_Empty_');
        if (!preg_match("/^1[3|4|5|7|8][0-9]\d{4,8}$/", $mobile_phone)) {
            $this->error_message('_MobilePhoneNum_Illegal_');
        }

        $uinfo = $this->checkToken($access_token);

        $varcode_result = Varcode::find()->where(['mobile_phone' => $mobile_phone, 'varcode'=>$varcode])->one();
        if ($varcode_result) {
            $member_model = Member::findIdentity($uinfo['id']);
            $member_model->mobile_phone = $mobile_phone;
            $member_model->mobile_phone_status = 1;
            if($member_model->save()){
                $this->success_message();
            }else{
                $this->error_message('_Bindings_Failure_');
            }
        }else{
            $this->error_message('_VerCode_Error_');
        }
    }



    //C-3.个人中心资料修改
    public function actionInfoUpdate(){
        $request = Yii::$app->request;

        $access_token = $request->post('access_token');
        $language = $request->post('language') == 'en_us'?'en_us':'zh_cn';
        $uinfo = $this->checkToken($access_token);
        $member_model = Member::findIdentity($uinfo['id']);

        $nickname = $request->post('nickname');
        if(!empty($nickname)){
			$member_model->nickname = $nickname;
        }

        if(!empty($_FILES['head_portrait']['tmp_name'])){
            /**
             * 图片配置名称
             */
            $type = 'imagesUpload';
            $stateMap = Yii::$app->params['uploadState'];

            // 图片上传配置
            $uploadConfig = Yii::$app->params[$type];
            $file = $_FILES['head_portrait'];
            $file_size = $file['size'];// 大小
            $file_name = $file['name'];// 原名称

            $file_exc = StringHelper::clipping($file_name);// 后缀

            if($file_size > $uploadConfig['maxSize'])// 判定大小是否超出限制
            {
                return $this->setResponse($stateMap['ERROR_SIZE_EXCEED']);
            }
            else if(!$this->checkType($file_exc, $type))// 检测类型
            {
                return $this->setResponse($stateMap['ERROR_TYPE_NOT_ALLOWED']);
            }
            else
            {
                // 相对路径
                if(!($path = $this->getPath($type)))
                {
                    return $this->setResponse(Yii::t($language,'_Folder_Creation_Failed__IsOpen_Attachment_Write_Permission_'));
                }

                $filePath = $path . $uploadConfig['prefix'] . StringHelper::random(10) . $file_exc;
                // 利用yii2自带的上传
                $uploadFile = UploadedFile::getInstanceByName('head_portrait');

                if($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath))
                {
                    $temp = [
                        'path' => $filePath,
                        'urlPath' => Yii::getAlias("@attachurl/") . $filePath,
                    ];
                    $member_model->head_portrait = $temp['urlPath'];
                }else{
                    return $this->setResponse(Yii::t($language,'_File_Move_Error_'));
                }
            }
        }
		if(empty($nickname) && empty($_FILES['head_portrait']['tmp_name'])){
			$this->check_empty('');
		}
		if($member_model->validate()){
			if($member_model->save()){
				$this->success_message();
			}else{
				$this->error_message('_Update_Failure_');
			}
		}else{
			$this->error_message(array_values($member_model->getFirstErrors())[0]);
		}
    }

    /**
     * 文件类型检测
     *
     * @param $ext
     * @param $type
     * @return bool
     */
    private function checkType($ext, $type)
    {
        if(empty(Yii::$app->params[$type]['maxExc']))
        {
            return true;
        }

        return in_array($ext, Yii::$app->params[$type]['maxExc']);
    }

    /**
     * 获取文件路径
     *
     * @param $type
     * @return string
     */
    public function getPath($type)
    {
        // 文件路径
        $file_path = Yii::$app->params[$type]['path'];
        // 子路径
        $sub_name = Yii::$app->params[$type]['subName'];
        $path = $file_path . date($sub_name,time()) . "/";
        $add_path = Yii::getAlias("@attachment/") . $path;
        // 创建路径
        FileHelper::mkdirs($add_path);
        return $path;
    }










}
