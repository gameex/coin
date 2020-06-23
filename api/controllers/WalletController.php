<?php 
/*
* Name: xiaocai
* Date: 2018-8-20 16:00
*/
namespace api\controllers;

use Yii;
use api\models\Coin;
use api\models\Member;
use Denpa\Bitcoin\Client as BitcoinClient;
use Denpa\Bitcoin\Omnicore as OmnicoreClient;
use api\models\MemberWallet;
use api\models\Varcode;

class WalletController extends ApibaseController
{
	public $modelClass = '';

	// 生成三个钱包地址【设置交易所密码】
	public function actionGenerateAddr()
	{
		$request      = Yii::$app->request;
		$access_token = $request->post('access_token');
		$uinfo        = $this->checkToken($access_token);

        // 检测参数信息
		$password      = $request->post('password');
		$password_confirm = $request->post('password_confirm');
		$this->check_empty($password, '_The_Password_Can_Not_Be_Empty_');
		$this->check_empty($password_confirm, '_Confirm_Password_Must_Not_Be_Empty_');
		if ($password != $password_confirm) {
			$this->error_message('_The_Two_Password_Input_Is_Inconsistent_');
		}

		// 判断用户是否认证【1：手机号，2：实名认证状态】
		if ($uinfo['mobile_phone_status'] == "0") {
			$this->error_message('_Users_Do_Not_Bind_Cell_Phone_Numbers_');
		}
		if ($uinfo['verified_status'] == "0") {
			//$this->error_message('_Users_Are_Not_Authenticated_By_Real_Names_');
		}

		// 存储密码
		$password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
		$member = Member::find()->where('id='.$uinfo['id'])->one();
		$member->password_hash = $password_hash;
		if (!$member->save(false)) {
			$this->error_message('Save Eroor#member');
		}

		$this->success_message();
	}

	// 修改交易所密码
	public function actionChangePassword()
	{
		$request      = Yii::$app->request;
		$access_token = $request->post('access_token');
		$uinfo        = $this->checkToken($access_token);

		// 检测参数
		$old_password = $request->post('old_password');
		$new_password = $request->post('new_password');
		$password_confirm = $request->post('password_confirm');
		$this->check_empty($old_password, '_The_Original_Password_Can_Not_Be_Empty_');
		$this->check_empty($new_password, '_The_New_Password_Can_Not_Be_Empty_');
		$this->check_empty($password_confirm, '_Confirm_Password_Must_Not_Be_Empty_');
		if ($new_password != $password_confirm) {
			$this->error_message('_The_Two_Password_Input_Is_Inconsistent_');
		}

		// 检测原密码是否正确
		$member = Member::find()->where('id='.$uinfo['id'])->one();
		$password_hash = $member->password_hash;
		if (!Yii::$app->getSecurity()->validatePassword($old_password, $password_hash)) {
			$this->error_message('_The_Original_Password_Is_Incorrect_');
		}

		// 存储新密码
		$password_hash = Yii::$app->getSecurity()->generatePasswordHash($new_password);
		$member->password_hash = $password_hash;
		if (!$member->save(false)) {
			$this->error_message('Save Error#member');
		}
		$this->success_message();
	}

	// 忘记交易所密码
	public function actionForgetPassword()
	{
		$request      = Yii::$app->request;
		$access_token = $request->post('access_token');
		$uinfo        = $this->checkToken($access_token);

		// 检测参数
		$phone_number     = $request->post('phone_number');
		$varcode          = $request->post('varcode');
		$new_password     = $request->post('new_password');
		$password_confirm = $request->post('password_confirm');
		$this->check_empty($phone_number, '_PhoneNum_Not_Empty_');
		$this->check_empty($varcode, '_The_Verification_Code_Can_Not_Be_Empty_');
		$this->check_empty($new_password, '_The_New_Password_Can_Not_Be_Empty_');
		$this->check_empty($password_confirm, '_Confirm_Password_Must_Not_Be_Empty_');
		if ($new_password != $password_confirm) {
			$this->error_message('_The_Two_Password_Input_Is_Inconsistent_');
		}

		// 验证是否是用户绑定的手机号
		$member = Member::find()->where('id='.$uinfo['id'])->one();
		$member_phone_number = $member->mobile_phone;
		if ((string)$phone_number != (string)$member_phone_number) {
			$this->error_message('_Verify_That_The_Phone_Number_Is_Not_Consistent_With_The_User_Mobile_Phone_Number_');
		}

		// 验证手机验证码是否正确
		$map = [
			'mobile_phone' => (string)$phone_number,
		];
		$code = Varcode::find()->where($map)->one()->varcode;
		if ((string)$varcode != (string)$code) {
			$this->error_message('_VerCode_Error_');
		}

		// 存储密码
		$password_hash = Yii::$app->getSecurity()->generatePasswordHash($new_password);
		$member->password_hash = $password_hash;
		if (!$member->save(false)) {
			$this->error_message('Save Error#member');
		}
		$this->success_message();
	}
}