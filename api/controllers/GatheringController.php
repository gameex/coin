<?php
/**
 * Created by PhpStorm.
 * User: landehua
 * Date: 2018/6/1 0001
 * Time: 14:11
 */

namespace api\controllers;

use common\helpers\Bankcard;
use common\helpers\FileHelper;
use common\helpers\StringHelper;
use common\models\Bank;
use common\models\MemberProceeds;
use common\models\Proceeds;
use Yii;
use yii\web\UploadedFile;
use Zxing\QrReader;

class GatheringController extends ApibaseController{
    public $modelClass = '';

    /*
     * 获取收款类型
     */
    public function actionGetMethod(){
        $method = Proceeds::find()->where(['status' => 1])->select("name,proceeds_type,is_qrcode,icon")->asArray()->all();
        if(!empty($method)){
            foreach($method as $key => $item){
                $method[$key]['icon'] = $this->get_user_avatar_url($item['icon']);
            }
        }
        $this->success_message($method);
    }

    /**
     * 获取收款信息列表
     */
    public function actionGetList(){
        $member_id = Yii::$app->user->identity->user_id;
        $data = MemberProceeds::find()->with('proceeds')->where(['member_id' => $member_id, 'is_delete' => 0])->asArray()->all();
		$method = Proceeds::find()->where(['status' => 1])->select("proceeds_type,is_qrcode")->asArray()->all();
        $method = array_column($method, 'is_qrcode', 'proceeds_type');
        $real_data = [];
        if(!empty($data)){
            foreach($data as $key => $item){
                $real_data[$key]['id'] = $item['id'];
                $real_data[$key]['member_id'] = $item['member_id'];
                $real_data[$key]['icon'] = $this->get_user_avatar_url($item['icon']);
                if(!empty($item['bank_name'])){
                    $real_data[$key]['name'] = $item['bank_name'];
                }else {
                    $real_data[$key]['name'] = $item['proceeds']['name'];
                }
                //$real_data[$key]['account'] = substr($item['account'], -4);
                $real_data[$key]['account'] = $item['account'];
                $real_data[$key]['proceeds_type'] = $item['proceeds_type'];
				$real_data[$key]['is_qrcode'] = $method[$item['proceeds_type']];
				$real_data[$key]['username'] = $item['username'];
                $real_data[$key]['qrcode'] = $this->get_user_avatar_url($item['qrcode']);
            }
        }else{
            $this->error_message('_No_Data_Query_');
        }
        $this->success_message($real_data);
    }

    public function actionGetOne(){
        $id = Yii::$app->request->post('id');
        $member_id = Yii::$app->user->identity->user_id;
        $this->check_empty($id,'_ID_Not_Empty_');
        $data = MemberProceeds::find()->with('proceeds')->select('id,proceeds_type,account,username,qrcode,bank_name,icon')->where(['member_id' => $member_id, 'id' => $id])->asArray()->one();
        if(!empty($data)){
            if($data['proceeds']['is_qrcode'] == 1) {
                $data['qrcode'] = $this->get_user_avatar_url($data['qrcode']);
            }
            $data['icon'] = $this->get_user_avatar_url($data['icon']);
            $data['proceeds']['icon'] = $this->get_user_avatar_url($data['proceeds']['icon']);
            unset($data['proceeds']['is_qrcode']);
            unset($data['proceeds']['status']);
            unset($data['proceeds']['ctime']);
    }
        $this->success_message($data);
    }

    /*
     * 添加收款信息
     */
    public function actionAddProceed(){
        $request = Yii::$app->request;
        $member_id = Yii::$app->user->identity->user_id;
        $proceeds_type = $request->post('proceeds_type');
        $account       = $request->post('account');
        $username      = $request->post('username', '');
        $bank_name     = $request->post('bank_name', '');
        $this->check_empty($proceeds_type, '_ReceivablesType_Not_Empty_');
        $this->check_empty($account, '_Account_Not_Empty_');
        $this->check_exist($member_id, $account);
        if($this->is_qrcode($proceeds_type) == 1){
            $qrcode = isset($_FILES['qrcode']) ? $_FILES['qrcode'] : '';
            $this->check_empty($qrcode, '_PaymentCode_Not_Empty_');
            $this->check_empty($qrcode['tmp_name'], '_PaymentCode_Not_Empty_');
            $qrcode = $this->upload($qrcode, 'qrcode');
            // $this->check_qrcode($qrcode);
            $icon = $this->get_icon($proceeds_type, 'no_bank');
        }else {
            $this->check_account($account);
            $this->check_empty($bank_name, '_BankOpen_Not_Empty_');
            $this->check_empty($username, '_CardName_Not_Empty');
            $icon = $this->get_icon($bank_name, 'bank');
            $qrcode = '';
        }
        if($this->save_info($member_id,$proceeds_type,$account,$bank_name,$icon,$qrcode,$username)){
            $this->success_message();
        }else{
            $this->error_message('_Save_Failure_Try_Again_');
        }
    }

    /**
     * 修改收款信息
     */
    public function actionUpProceed(){
        $request = Yii::$app->request;
        $member_id = Yii::$app->user->identity->user_id;
        $id = $request->post('id');
        $proceeds_type = $request->post('proceeds_type');
        $account       = $request->post('account');
        $username      = $request->post('username', '');
        $bank_name     = $request->post('bank_name', '');
        $this->check_empty($id, '_ID_Not_Empty_');
        $this->check_empty($proceeds_type, '_ReceivablesType_Not_Empty_');
        $this->check_empty($account, '_Account_Not_Empty_');
        $exist = MemberProceeds::find()->where(['member_id' => $member_id, 'account' => $account, 'is_delete' => 0])->andWhere(['!=', 'id', $id])->asArray()->one();
        if(!empty($exist)){
            $this->error_message('_Have_Add__Account_Num_');
        }
        if($this->is_qrcode($proceeds_type) == 1){
            $qrcode = isset($_FILES['qrcode']) ? $_FILES['qrcode'] : '';
            $this->check_empty($qrcode, '_PaymentCode_Not_Empty_');
            $this->check_empty($qrcode['tmp_name'], '_PaymentCode_Not_Empty_');
            $qrcode = $this->upload($qrcode, 'qrcode');
            // $this->check_qrcode($qrcode);
            $icon = $this->get_icon($proceeds_type, 'no_bank');
        }else {
            //$this->check_account($account);
            $this->check_empty($bank_name, '_BankOpen_Not_Empty_');
            $this->check_empty($username, '_CardName_Not_Empty');
            $icon = $this->get_icon($bank_name, 'bank');
            $qrcode = '';
        }
        if($this->up_info($id,$member_id,$proceeds_type,$account,$bank_name,$icon,$qrcode,$username)){
            $this->success_message();
        }else{
            $this->error_message('_Save_Failure_Try_Again_');
        }
    }

    /**
     * 删除收款信息
     */
    public function actionDelProceed(){
        $request = Yii::$app->request;
        $member_id = Yii::$app->user->identity->user_id;
        $id = $request->post('id');
        $this->check_empty($id, '_ID_Not_Empty_');
        $model = MemberProceeds::findOne(['id' => $id, 'member_id' => $member_id]);
        if(!empty($model)){
            $model->is_delete = 1;
            if($model->save()){
                $this->success_message([],'_Delete_Success_');
            }else{
                $this->error_message('_Delete_Failure_');
            }
        }else{
            $this->error_message('_Delete_Failure_');
        }
    }

    /**
     * 获取开户行列表
     */
    public function actionGetBank(){
        $method = Bank::find()->where(['status' => 1])->asArray()->all();
        if(!empty($method)){
            foreach($method as $key => $item){
                $method[$key]['icon'] = $this->get_user_avatar_url($item['icon']);
            }
        }
        $this->success_message($method);
    }

    private function get_icon($key, $type){
        if($type == 'bank'){
            return Bank::findOne(['bank_name' => $key])->icon;
        }else{
            return Proceeds::findOne(['proceeds_type' => $key])->icon;
        }
    }

    /**
     * 检查是否需要上传付款码
     * @param $proceeds_type
     * @return int
     */
    private function is_qrcode($proceeds_type){
        $model = Proceeds::findOne(['proceeds_type' => $proceeds_type]);
        if(!empty($model)){
            return $model->is_qrcode;
        }else {
            $this->error_message('_ReceivablesType_Not_Exist_');
        }
    }

    /**
     * 检查账号格式是否正确
     * 支付宝微信可以是任一账号：手机号  邮箱等
     * 银行卡必须是银行卡号
     * @param $account
     * @return bool
     */
    private function check_account($account){
        $Bank = new Bankcard();
        $result = $Bank->Luhn($account);
        if($result){
            return true;
        }else{
            $this->error_message('_Account_Format_wrong_PCheck_');
        }
    }

    /**
     * 检查二维码是否有效
     * @param $qrcode
     * @return bool
     */
    private function check_qrcode($qrcode){
        $model = new QrReader($qrcode['path']);
        $text = $model->text();
        if(empty($text)){
            $this->error_message('_Upload_Correct_True_Code_');
        }else {
            return true;
        }
    }

    /**
     * @param $id
     * @param $member_id
     * @param $proceeds_type
     * @param $account
     * @param string $bank_name
     * @param $icon
     * @param string $qrcode
     * @param string $username
     * @return bool
     */
    private function up_info($id,$member_id,$proceeds_type,$account,$bank_name='',$icon,$qrcode='',$username=''){
        $model = MemberProceeds::findOne(['id' => $id, 'member_id' => $member_id]);
        if(!empty($model)) {
            $model->proceeds_type = $proceeds_type;
            $model->account = $account;
            $model->bank_name = $bank_name;
            $model->icon = $icon;
            if($this->is_qrcode($proceeds_type) == 1) {
                $model->qrcode = $qrcode['urlPath'];
            }else{
                $model->qrcode = $qrcode;
            }
            $model->username = $username;
            $model->ctime = date("Y-m-d H:i:s");
            return $model->save();
        }else {
            return false;
        }
    }

    /**
     * 保存收款信息
     * @param $member_id
     * @param $proceeds_type
     * @param $account
     * @param string $bank_name
     * @param $icon
     * @param string $qrcode
     * @param string $username
     * @return bool
     */
    private function save_info($member_id,$proceeds_type,$account,$bank_name='',$icon,$qrcode='',$username=''){
        $model = new MemberProceeds();
        $model->member_id = $member_id;
        $model->proceeds_type = $proceeds_type;
        $model->account = $account;
        $model->bank_name = $bank_name;
        $model->icon = $icon;
        if($this->is_qrcode($proceeds_type) == 1) {
            $model->qrcode = $qrcode['urlPath'];
        }else{
            $model->qrcode = $qrcode;
        }
        $model->username = $username;
        $model->ctime = date("Y-m-d H:i:s");
        return $model->save();
    }

    /*
     * 判断账号是否添加过
     */
    private function check_exist($member_id, $account){
        $exist = MemberProceeds::find()->where(['member_id' => $member_id, 'account' => $account, 'is_delete' => 0])->one();
        if(!empty($exist)){
            $this->error_message('_Have_Add__Account_Num_');
        }
    }

    /**
     * @param $file
     * @param $name
     * @return array
     */
    private function upload($file,$name){
        $type = 'imagesUpload';
        $uploadConfig = Yii::$app->params[$type];
        $stateMap = Yii::$app->params['uploadState'];
        $file_size = $file['size'];
        $file_name = $file['name'];
        $file_exc = StringHelper::clipping($file_name);

        if ($file_size > $uploadConfig['maxSize']){
            $message = $stateMap['ERROR_SIZE_EXCEED'];
            $this->error_message($message);
        } else if (!$this->checkType($file_exc, $type)){
            $message = $stateMap['ERROR_TYPE_NOT_ALLOWED'];
            $this->error_message($message);
        } else {
            if (!($path = $this->getPath($type))) {
                $message = '_Folder_Creation_Failed__IsOpen_Attachment_Write_Permission_';
                $this->error_message($message);
            }
            $filePath = $path . $uploadConfig['prefix'] . StringHelper::random(10) . $file_exc;
            $uploadFile = UploadedFile::getInstanceByName($name);
            if ($uploadFile->saveAs(Yii::getAlias("@attachment/") . $filePath)) {
                $data = [
                    'path' => Yii::getAlias("@attachment/") . $filePath,
                    'urlPath' => Yii::getAlias("@attachurl/") . $filePath,
                ];
                return $data;
            } else {
                $message = '_File_Move_Error_';
                $this->error_message($message);
            }
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